<?php

class JoTable
{
    // Constants for default values
    const DEFAULT_DATE_FORMAT = 'd/m/Y';
    const DEFAULT_TABLE_CLASS = 'table table-bordered table-hover table-striped';
    const DEFAULT_HEADER_CLASS = 'text-center';
    private ?array $paginationConfig = null;  // ເພີ່ມບັນທັດນີ້

    // Properties with typed declarations
    private string $classTable;
    private string $classHeader;
    private string $classCellHeader;
    private string $classBody;
    private array $tableHeader;
    private array $tableData;
    private int $No;
    private string $tableID;
    private ?string $searchQuery;
    private array $sortConfig;
    private array $exportConfig;
    private string $csrf_token;

    /**
     * Constructor with parameter type declarations
     */
    public function __construct(string ...$header)
    {
        $this->classTable = self::DEFAULT_TABLE_CLASS;
        $this->classHeader = self::DEFAULT_HEADER_CLASS;
        $this->classCellHeader = '';
        $this->classBody = '';
        $this->tableHeader = [];
        $this->tableData = [];
        $this->No = 1;
        $this->tableID = 'mytable';
        $this->searchQuery = null;
        $this->sortConfig = [];
        $this->exportConfig = [];
        $this->csrf_token = $this->generateCSRFToken();

        if (!empty($header)) {
            $this->createHeader($header);
        }
    }

    /**
     * Generate and store CSRF token
     */
    private function generateCSRFToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }

    /**
     * Sanitize input data
     */
    private function sanitizeInput($data): string
    {
        if (is_string($data)) {
            return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
        }
        return $data;
    }

    /**
     * Enable table search functionality
     */
    public function enableSearch(array $searchableColumns = []): void
    {

        $this->searchQuery = "";
        $searchHtml = '
        <div class="mb-3">
            <input type="text" class="form-control" id="tableSearch" 
                   placeholder="ຄົ້ນຫາ..." onkeyup="searchTable()">
        </div>
        <script>
        function searchTable() {
            const input = document.getElementById("tableSearch");
            const filter = input.value.toLowerCase();
            const table = document.getElementById("' . $this->tableID . '");
            const rows = table.getElementsByTagName("tr");

            for (let i = 1; i < rows.length; i++) {
                let row = rows[i];
                let cells = row.getElementsByTagName("td");
                let found = false;
                
                for (let j = 0; j < cells.length; j++) {
                    let cell = cells[j];
                    if (cell) {
                        let text = cell.textContent || cell.innerText;
                        if (text.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                row.style.display = found ? "" : "none";
            }
        }
        </script>';

        array_push($this->tableData, $searchHtml);
    }

    /**
     * Enable table export functionality
     */
    public function enableExport(array $formats = ['csv', 'excel']): void
    {
        $this->exportConfig = $formats;
        $exportHtml = '<div class="mb-3">';

        if (in_array('csv', $formats)) {
            $exportHtml .= '<button onclick="exportTableToCSV()" class="btn btn-sm btn-primary me-2">
                           Export to CSV</button>';
        }
        if (in_array('excel', $formats)) {
            $exportHtml .= '<button onclick="exportTableToExcel()" class="btn btn-sm btn-success">
                           Export to Excel</button>';
        }

        $exportHtml .= '</div>';
        $exportHtml .= $this->getExportScripts();

        array_push($this->tableData, $exportHtml);
    }

    /**
     * Get export functionality scripts
     */
    private function getExportScripts(): string
    {
        return '<script>
        function exportTableToCSV() {
            const table = document.getElementById("' . $this->tableID . '");
            const rows = table.getElementsByTagName("tr");
            let csv = [];
            
            for (let i = 0; i < rows.length; i++) {
                const row = [], cols = rows[i].getElementsByTagName("td");
                for (let j = 0; j < cols.length; j++) {
                    row.push(cols[j].innerText);
                }
                csv.push(row.join(","));
            }
            
            const csvFile = new Blob([csv.join("\\n")], {type: "text/csv"});
            const downloadLink = document.createElement("a");
            downloadLink.download = "table-export.csv";
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
        }
        
        function exportTableToExcel() {
            const table = document.getElementById("' . $this->tableID . '");
            const html = table.outerHTML;
            const url = "data:application/vnd.ms-excel," + encodeURIComponent(html);
            const downloadLink = document.createElement("a");
            document.body.appendChild(downloadLink);
            downloadLink.href = url;
            downloadLink.download = "table-export.xls";
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
        </script>';
    }

    /**
     * Render the table
     */
    public function render(): void
    {
        $html = '';

        // ສະແດງ export buttons ຖ້າມີການເປີດໃຊ້
        if (!empty($this->exportConfig)) {
            $exportHtml = '<div class="mb-3">';
            if (in_array('csv', $this->exportConfig)) {
                $exportHtml .= '<button onclick="exportTableToCSV()" class="btn btn-sm btn-primary me-2">
                               Export to CSV</button>';
            }
            if (in_array('excel', $this->exportConfig)) {
                $exportHtml .= '<button onclick="exportTableToExcel()" class="btn btn-sm btn-success">
                               Export to Excel</button>';
            }
            $exportHtml .= '</div>';
            $exportHtml .= $this->getExportScripts();
            $html .= $exportHtml;
        }

        // ສະແດງ search box ຖ້າມີການເປີດໃຊ້
        if ($this->searchQuery !== null) {
            $html .= '
            <div class="mb-3 d-flex justify-content-end">
            <div class="col-4">
                <input type="text" class="form-control" id="tableSearch" 
                    placeholder="ຄົ້ນຫາ..." onkeyup="searchTable()">
                </div>
            </div>
            <script>
            function searchTable() {
                const input = document.getElementById("tableSearch");
                const filter = input.value.toLowerCase();
                const table = document.getElementById("' . $this->tableID . '");
                const rows = table.getElementsByTagName("tr");
    
                for (let i = 1; i < rows.length; i++) {
                    let row = rows[i];
                    let cells = row.getElementsByTagName("td");
                    let found = false;
                    
                    for (let j = 0; j < cells.length; j++) {
                        let cell = cells[j];
                        if (cell) {
                            let text = cell.textContent || cell.innerText;
                            if (text.toLowerCase().indexOf(filter) > -1) {
                                found = true;
                                break;
                            }
                        }
                    }
                    row.style.display = found ? "" : "none";
                }
            }
            </script>';
        }

        // ສະແດງຕາຕະລາງ
        $html .= "<table class='" . htmlspecialchars($this->classTable) . "' id='" . htmlspecialchars($this->tableID) . "'>\n";

        // Header
        $html .= "<thead class='" . htmlspecialchars($this->classHeader) . "'><tr>\n";
        foreach ($this->tableHeader as $cell) {
            $html .= $cell . "\n";
        }
        $html .= "</tr></thead>\n";

        // Body
        $html .= "<tbody" . (empty($this->classBody) ? "" : " class='" . htmlspecialchars($this->classBody) . "'") . ">\n";


        // ແຍກຂໍ້ມູນ body ແລະ footer
        $bodyContent = [];
        $footerContent = [];

        foreach ($this->tableData as $row) {
            if (is_array($row) && $row['type'] === 'footer') {
                $footerContent[] = $row['content'];
            } elseif (strpos($row, '<tr') === 0) {
                $bodyContent[] = $row;
            }
        }

        // ສະແດງ body rows
        foreach ($bodyContent as $row) {
            $html .= $row . "\n";
        }

        $html .= "</tbody>\n";

        // ສະແດງ footer ຖ້າມີ
        if (!empty($footerContent)) {
            $html .= "<tfoot>\n";
            foreach ($footerContent as $row) {
                $html .= $row . "\n";
            }
            $html .= "</tfoot>\n";
        }

        $html .= "</table>\n";

        // ເພີ່ມ Pagination ຖ້າມີການເປີດໃຊ້
        if ($this->paginationConfig !== null) {
            $html .= $this->renderPagination();
        }

        echo $html;
    }

    /**
     * Add a new row with data validation
     */
    public function addRow(array $cells = [], string $attribute = ""): void
    {
        // ເລີ່ມຕົ້ນດ້ວຍການສ້າງ row
        $sanitizedAttr = $this->sanitizeInput($attribute);
        $row = "<tr" . ($sanitizedAttr ? " $sanitizedAttr" : "") . ">";

        // ເພີ່ມແຕ່ລະ cell ເຂົ້າໄປໃນ row
        foreach ($cells as $cell) {
            // ໃຊ້ cell ໂດຍກົງຖ້າມັນຖືກສົ່ງມາຈາກ cell() method
            if (strpos($cell, '<td') === 0) {
                $row .= $cell;
            } else {
                // ຖ້າເປັນ string ທຳມະດາໃຫ້ສ້າງ cell ໃໝ່
                $row .= $this->cell($cell);
            }
        }

        $row .= "</tr>";
        $this->tableData[] = $row;
    }

    /**
     * Create action buttons with CSRF protection
     */
    public function cellAction(...$actions): string
    {
        $td = "<td><div class='d-flex gap-1 justify-content-center'>";
        foreach ($actions as $action) {
            if (strpos($action, 'delete') !== false) {
                $action = str_replace(
                    'href=',
                    "onclick='return confirmDelete(event)' data-csrf='{$this->csrf_token}' href=",
                    $action
                );
            }
            $td .= $action;
        }
        $td .= "</div></td>";

        // Add confirmation script for delete actions
        $td .= "<script>
            function confirmDelete(event) {
                if (!confirm('ທ່ານແນ່ໃຈບໍ່ວ່າຕ້ອງການລຶບ?')) {
                    event.preventDefault();
                    return false;
                }
                const csrf = event.target.getAttribute('data-csrf');
                const url = new URL(event.target.href);
                url.searchParams.append('csrf_token', csrf);
                event.target.href = url.toString();
                return true;
            }
        </script>";

        return $td;
    }

    /**
     * Render pagination controls
     */
    private function renderPagination(): string
    {
        $itemsPerPage = $this->paginationConfig['itemsPerPage'];
        $pageSizes = $this->paginationConfig['pageSizes'];
        $showFirstLast = $this->paginationConfig['showFirstLast'];
        $showPageInfo = $this->paginationConfig['showPageInfo'];

        $sizeOptions = '';
        foreach ($pageSizes as $size) {
            $selected = $size === $itemsPerPage ? 'selected' : '';
            $sizeOptions .= "<option value='{$size}' {$selected}>{$size} ລາຍການ</option>";
        }

        return "<div class='d-flex justify-content-between align-items-center mt-3'>
                    <div>
                        <select id='itemsPerPage' class='form-select form-select-sm' 
                                style='width: auto;' onchange='changeItemsPerPage()'>
                            {$sizeOptions}
                        </select>
                    </div>
                    <div id='tablePagination' class='d-flex gap-1'></div>
                    " . ($showPageInfo ? "<div id='pageInfo' class='text-muted small'></div>" : "") . "
                </div>
                <script>
                function changeItemsPerPage() {
                    const itemsPerPage = parseInt(document.getElementById('itemsPerPage').value);
                    paginate(1, itemsPerPage);
                }
                
                function paginate(page, itemsPerPage) {
                    const table = document.getElementById('" . $this->tableID . "');
                    const rows = Array.from(table.getElementsByTagName('tr')).slice(1);
                    const totalRows = rows.length;
                    const totalPages = Math.ceil(totalRows / itemsPerPage);
                    
                    // ເຊື່ອງທຸກແຖວ
                    rows.forEach(row => row.style.display = 'none');
                    
                    // ສະແດງແຖວສຳລັບໜ້າປະຈຸບັນ
                    const start = (page - 1) * itemsPerPage;
                    const end = Math.min(start + itemsPerPage, totalRows);
                    for (let i = start; i < end; i++) {
                        if (rows[i]) {
                            rows[i].style.display = '';
                        }
                    }
                    
                    // ອັບເດດປຸ່ມ pagination
                    updatePaginationButtons(page, totalPages, itemsPerPage);
                    
                    " . ($showPageInfo ? "
                    // ອັບເດດຂໍ້ມູນໜ້າ
                    const pageInfo = document.getElementById('pageInfo');
                    pageInfo.textContent = `ສະແດງ \${start + 1}-\${end} ຈາກ \${totalRows} ລາຍການ`;
                    " : "") . "
                }
                
                function updatePaginationButtons(currentPage, totalPages, itemsPerPage) {
                    const pagination = document.getElementById('tablePagination');
                    let buttons = '';
                    
                    " . ($showFirstLast ? "
                    // ປຸ່ມ First
                    buttons += `<button class='btn btn-sm btn-outline-primary' 
                               onclick='paginate(1, \${itemsPerPage})'
                               \${currentPage === 1 ? 'disabled' : ''}>
                               <i class='bi bi-chevron-double-left'></i>
                               </button>`;
                    " : "") . "
                    
                    // ປຸ່ມ Previous
                    buttons += `<button class='btn btn-sm btn-outline-primary' 
                               onclick='paginate(\${currentPage - 1}, \${itemsPerPage})'
                               \${currentPage === 1 ? 'disabled' : ''}>
                               <i class='bi bi-chevron-left'></i>
                               </button>`;
                    
                    // ປຸ່ມໝາຍເລກໜ້າ
                    let startPage = Math.max(1, currentPage - 2);
                    let endPage = Math.min(totalPages, startPage + 4);
                    if (endPage - startPage < 4) {
                        startPage = Math.max(1, endPage - 4);
                    }
                    
                    for (let i = startPage; i <= endPage; i++) {
                        buttons += `<button class='btn btn-sm \${i === currentPage ? 'btn-primary' : 'btn-outline-primary'}' 
                                   onclick='paginate(\${i}, \${itemsPerPage})'>\${i}</button>`;
                    }
                    
                    // ປຸ່ມ Next
                    buttons += `<button class='btn btn-sm btn-outline-primary' 
                               onclick='paginate(\${currentPage + 1}, \${itemsPerPage})'
                               \${currentPage === totalPages ? 'disabled' : ''}>
                               <i class='bi bi-chevron-right'></i>
                               </button>`;
                    
                    " . ($showFirstLast ? "
                    // ປຸ່ມ Last
                    buttons += `<button class='btn btn-sm btn-outline-primary' 
                               onclick='paginate(\${totalPages}, \${itemsPerPage})'
                               \${currentPage === totalPages ? 'disabled' : ''}>
                               <i class='bi bi-chevron-double-right'></i>
                               </button>`;
                    " : "") . "
                    
                    pagination.innerHTML = buttons;
                }
                
                // ເລີ່ມຕົ້ນ pagination
                document.addEventListener('DOMContentLoaded', () => {
                    const itemsPerPage = parseInt(document.getElementById('itemsPerPage').value);
                    paginate(1, itemsPerPage);
                });
                </script>";
    }

    /**
     * Format date with validation
     */
    public function formatDate(string $dateString, string $format = self::DEFAULT_DATE_FORMAT): string
    {
        $date = DateTime::createFromFormat('Y-m-d', $dateString);
        if ($date === false) {
            throw new InvalidArgumentException('Invalid date format. Expected Y-m-d');
        }
        return $date->format($format);
    }

    /**
     * Format money with validation
     */
    public function formatMoney($amount, string $currency = ''): string
    {
        if (!is_numeric($amount)) {
            throw new InvalidArgumentException('Amount must be numeric');
        }
        return number_format($amount) . ($currency ? ' ' . $currency : '');
    }

    /**
     * Create header cells with validation
     */
    private function createHeader(array $header): void
    {
        foreach ($header as $cell) {
            $this->tableHeader[] = $this->cellHeader(
                $this->sanitizeInput($cell),
                "class='align-middle'"
            );
        }
    }

    /**
     * Add footer row with validation
     */
    public function addRowFoot(array $cells = [], string $attribute = ""): void
    {
        // ແກ້ໄຂການສ້າງແຖວຂອງ footer
        $tr = "<tr" . ($attribute ? " $attribute" : "") . ">";
        foreach ($cells as $cell) {
            $tr .= is_string($cell) ? $cell : $cell;
        }
        $tr .= "</tr>";

        // ເກັບຂໍ້ມູນໄວ້ສະເພາະ tr ແຖວຂອງ footer
        $this->tableData[] = ['type' => 'footer', 'content' => $tr];
    }

    /**
     * Add multiple footer rows with validation
     */
    public function addRowFoots(array $cells = [], string $attribute = ""): void
    {
        $foot = "<tfoot>";
        foreach ($cells as $cel) {
            if (!is_array($cel)) {
                throw new InvalidArgumentException('Footer cells must be an array');
            }
            $tr = "<tr " . $this->sanitizeInput($attribute) . ">";
            foreach ($cel as $row) {
                $tr .= is_string($row) ? $this->sanitizeInput($row) : $row;
            }
            $tr .= "</tr>";
            $foot .= $tr;
        }
        $foot .= "</tfoot>";
        $this->tableData[] = $foot;
    }

    /**
     * Add custom HTML row with validation
     */
    public function addRowCustom(string $html): void
    {
        if (empty(trim($html))) {
            throw new InvalidArgumentException('HTML content cannot be empty');
        }
        array_push($this->tableData, $this->sanitizeInput($html));
    }

    /**
     * Create table cell with validation
     * @param mixed $text Cell content
     * @param string $attribute Additional HTML attributes
     * @return string HTML TD element
     * @throws InvalidArgumentException If text is not string, int, or float
     */
    public function cell($text, string $attribute = ""): string
    {
        if (!is_string($text) && !is_int($text) && !is_float($text)) {
            throw new InvalidArgumentException('Cell text must be string, integer, or float');
        }

        // ຮັກສາ HTML attributes ທີ່ຖືກຕ້ອງ (ເຊັ່ນ colspan, class)
        $sanitizedText = htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
        return "<td" . ($attribute ? " $attribute" : "") . ">{$sanitizedText}</td>";
    }

    /**
     * Create header cell with validation
     * @param string|int|float $text Header content
     * @param string $attribute Additional HTML attributes
     * @return string HTML TH element
     */
    public function cellHeader($text, string $attribute = ""): string
    {
        if (!is_string($text) && !is_int($text) && !is_float($text)) {
            throw new InvalidArgumentException('Header text must be string, integer, or float');
        }

        $textStr = (string)$text;
        $attr = empty($attribute) ? "" : " " . $this->sanitizeInput($attribute);
        $sanitizedText = $this->sanitizeInput($textStr);

        return "<th{$attr}>{$sanitizedText}</th>";
    }

    /**
     * Set table ID with validation
     */
    public function setID(string $id): void
    {
        if (empty(trim($id))) {
            throw new InvalidArgumentException('Table ID cannot be empty');
        }
        $this->tableID = preg_replace('/[^A-Za-z0-9\-_]/', '', $id);
    }

    /**
     * Set table class with validation
     */
    public function setClassTable(string $classTable): void
    {
        if (empty(trim($classTable))) {
            throw new InvalidArgumentException('Table class cannot be empty');
        }
        $this->classTable = $this->sanitizeInput($classTable);
    }

    /**
     * Set header class with validation
     */
    public function setClassHeader(string $class): void
    {
        $this->classHeader = $this->sanitizeInput($class);
    }

    /**
     * Set specific header cell with validation
     */
    public function setHeader(int $index, string ...$cell): string
    {
        if ($index < 0 || $index >= count($this->tableHeader)) {
            throw new InvalidArgumentException('Invalid header index');
        }

        $arr = $this->getAttr($cell);
        $class = empty($arr['class'] ?? "") ? "" : "class='" . $this->sanitizeInput($arr['class']) . "'";
        $text = empty($arr['text'] ?? "") ? "" : $this->sanitizeInput($arr['text']);

        return $this->tableHeader[$index] = "<td {$class}>{$text}</td>";
    }

    /**
     * Set body class with validation
     */
    public function setClassBody(string $class): void
    {
        $this->classBody = $this->sanitizeInput($class);
    }

    /**
     * Get current auto-increment number
     */
    public function autoNum(): int
    {
        return $this->No++;
    }

    /**
     * Parse attributes with validation
     */
    private function getAttr(array $arr = []): array
    {
        $outputArray = [];
        foreach ($arr as $element) {
            if (!is_string($element)) {
                throw new InvalidArgumentException('Attribute must be a string');
            }
            $pair = explode(':', $element);
            if (count($pair) !== 2) {
                throw new InvalidArgumentException('Invalid attribute format');
            }
            $outputArray[$this->sanitizeInput($pair[0])] = htmlspecialchars_decode($this->sanitizeInput($pair[1]));
        }
        return $outputArray;
    }

    /**
     * Create action info button with validation
     */
    public function actionInfo(
        string $url = "#",
        bool $hidden = true,
        string $text = '<i class="bi bi-eye-fill"></i>',
        string $class = "btn btn-sm btn-info"
    ): string {
        $url = $this->sanitizeInput($url);
        // ບໍ່ຕ້ອງ sanitize text ທີ່ເປັນ HTML icon
        $class = $this->sanitizeInput($class);
        $ishidden = $hidden ? "" : " hidden";
        return "<a href='{$url}' class='{$class}'{$ishidden}>{$text}</a>";
    }

    /**
     * Create action update button with validation
     */
    public function actionUpdate(
        string $url = "#",
        bool $hidden = true,
        string $text = '<i class="bi bi-pencil-square"></i>',
        string $class = "btn btn-sm btn-success"
    ): string {
        $url = $this->sanitizeInput($url);
        // ບໍ່ຕ້ອງ sanitize text ທີ່ເປັນ HTML icon
        $class = $this->sanitizeInput($class);
        $ishidden = $hidden ? "" : " hidden";
        return "<a href='{$url}' class='{$class}'{$ishidden}>{$text}</a>";
    }

    /**
     * Create action delete button with validation
     */
    public function actionDelete(
        string $url = "#",
        bool $hidden = true,
        string $text = '<i class="bi bi-trash-fill"></i>',
        string $class = "btn btn-sm btn-danger"
    ): string {
        $url = $this->sanitizeInput($url);
        // ບໍ່ຕ້ອງ sanitize text ທີ່ເປັນ HTML icon
        $class = $this->sanitizeInput($class);
        $ishidden = $hidden ? "" : " hidden";
        return "<a href='{$url}' class='{$class}'{$ishidden}>{$text}</a>";
    }

    /**
     * Set header cell class with validation
     */
    public function setClassCellHeader(string $classCellHeader): void
    {
        $this->classCellHeader = $this->sanitizeInput($classCellHeader);
    }

    /**
     * Add sorting functionality to table
     */
    public function enableSorting(array $sortableColumns = []): void
    {
        $script = "
        <script>
        function sortTable(columnIndex) {
            const table = document.getElementById('" . $this->tableID . "');
            const rows = Array.from(table.rows).slice(1);
            const direction = table.getAttribute('data-sort') === 'asc' ? -1 : 1;
            
            rows.sort((row1, row2) => {
                const cell1 = row1.cells[columnIndex].textContent;
                const cell2 = row2.cells[columnIndex].textContent;
                
                if (!isNaN(cell1) && !isNaN(cell2)) {
                    return direction * (Number(cell1) - Number(cell2));
                }
                return direction * cell1.localeCompare(cell2);
            });
            
            table.setAttribute('data-sort', direction === 1 ? 'asc' : 'desc');
            rows.forEach(row => table.tBodies[0].appendChild(row));
        }
        </script>";

        // Add click events to sortable columns
        foreach ($this->tableHeader as $index => $header) {
            if (empty($sortableColumns) || in_array($index, $sortableColumns)) {
                $this->tableHeader[$index] = str_replace(
                    '<th',
                    "<th style='cursor: pointer' onclick='sortTable($index)'",
                    $header
                );
            }
        }

        array_push($this->tableData, $script);
    }

    /**
     * Enable pagination functionality with configurable options
     *
     * @param array{
     *   itemsPerPage?: int,
     *   pageSizes?: array<int>,
     *   showFirstLast?: bool,
     *   showPageInfo?: bool
     * } $options Configuration options for pagination
     */
    public function enablePagination(array $options = []): void
    {
        // ກວດສອບຄ່າທີ່ສົ່ງມາ
        if (isset($options['itemsPerPage']) && (!is_int($options['itemsPerPage']) || $options['itemsPerPage'] < 1)) {
            throw new InvalidArgumentException('itemsPerPage must be a positive integer');
        }

        if (isset($options['pageSizes']) && !is_array($options['pageSizes'])) {
            throw new InvalidArgumentException('pageSizes must be an array');
        }

        if (isset($options['showFirstLast']) && !is_bool($options['showFirstLast'])) {
            throw new InvalidArgumentException('showFirstLast must be boolean');
        }

        if (isset($options['showPageInfo']) && !is_bool($options['showPageInfo'])) {
            throw new InvalidArgumentException('showPageInfo must be boolean');
        }

        // ຄ່າເລີ່ມຕົ້ນ
        $defaultOptions = [
            'itemsPerPage' => 10,
            'pageSizes' => [10, 25, 50, 100],
            'showFirstLast' => true,
            'showPageInfo' => true
        ];

        $this->paginationConfig = array_merge($defaultOptions, $options);
    }
}
