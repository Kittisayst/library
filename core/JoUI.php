<?php

class JoUI
{
    /**
     * Sanitize and validate attributes
     */
    private static function sanitizeAttributes($attrs)
    {
        $sanitized = [];
        foreach ($attrs as $key => $value) {
            $sanitized[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        return $sanitized;
    }

    /**
     * Convert attribute array to key-value pairs
     */
    private static function getAttr($arr = [])
    {
        try {
            $outputArray = [];
            foreach ($arr as $element) {
                if (!is_string($element)) {
                    throw new InvalidArgumentException('Invalid attribute format');
                }

                $pair = explode(':', $element);
                if (count($pair) !== 2) {
                    throw new InvalidArgumentException('Invalid attribute pair format');
                }

                $outputArray[$pair[0]] = htmlspecialchars_decode($pair[1]);
            }
            return $outputArray;
        } catch (Exception $e) {
            error_log("Error in getAttr: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Create an accordion component
     */
    public static function Accordion($datas = [], ...$attr)
    {
        $arr = self::getAttr($attr);
        $arr = self::sanitizeAttributes($arr);

        $class = $arr["class"] ?? "accordion";
        $id = empty($arr["id"]) ? "accordionExample" : $arr['id'];
        $selectIndex = $arr["index"] ?? 0;
        $accordionData = empty($datas) ?
            ["Item #1" => "content1", "Item #2" => "content2", "Item #3" => "content3"] :
            $datas;

        $html = "<div class='$class' id='$id'>";
        $index = 0;

        foreach ($accordionData as $key => $value) {
            $isSelect = $index == $selectIndex ? "true" : "false";
            $iscollapsed = $index == $selectIndex ? "" : " collapsed";
            $ishow = $isSelect == "true" ? " show" : "";

            $html .= "<div class='accordion-item'>";
            $html .= "<h2 class='accordion-header'>";
            $html .= self::Button(
                "text:$key",
                "class:accordion-button$iscollapsed",
                "data-bs-toggle:collapse",
                "data-bs-target:#collapse$index",
                "aria-expanded:$isSelect",
                "aria-controls:collapse$index"
            );
            $html .= "</h2>";
            $html .= "<div id='collapse$index' class='accordion-collapse collapse$ishow' data-bs-parent='#$id'>
                    <div class='accordion-body'>$value</div>
                    </div>";
            $html .= "</div>";
            $index++;
        }

        $html .= "</div>";
        return $html;
    }

    /**
     * Create an alert component
     */
    public static function Alert(...$attr)
    {
        $arr = self::getAttr($attr);
        $arr = self::sanitizeAttributes($arr);

        $class = $arr['class'] ?? "alert";
        $color = $arr['color'] ?? "primary";
        $text = $arr['text'] ?? "A simple primary alert—check it out!";
        $hidden = empty($arr['hidden']) ? "" : " hidden";
        $id = empty($arr['id']) ? "" : " id='{$arr['id']}'";

        return "<div$id class='$class alert-$color' role='alert'$hidden>$text</div>";
    }

    /**
     * Create a badge component
     */
    public static function Badge(...$attrs)
    {
        $arr = self::getAttr($attrs);
        $arr = self::sanitizeAttributes($arr);

        $color = $arr['color'] ?? "danger";
        $text = $arr['text'] ?? "99+";
        $position = $arr['position'] ?? "position-absolute top-0 start-100 translate-middle";
        $class = empty($arr['class']) ?
            "$position badge rounded-pill bg-$color" :
            $arr['class'];

        return "<span class='$class'>$text</span>";
    }

    /**
     * Create a button component
     */
    public static function Button(...$attr)
    {
        $arr = self::getAttr($attr);
        $arr = self::sanitizeAttributes($arr);

        $type = $arr['type'] ?? "button";
        $text = $arr['text'] ?? "Button";
        $name = empty($arr['name']) ? "" : " name='{$arr['name']}'";
        $id = empty($arr['id']) ? "" : " id='{$arr['id']}'";
        $outline = empty($arr['outline']) ? "" : "outline-";
        $color = empty($arr['color']) ? " btn-primary" : " btn-$outline{$arr['color']}";
        $size = empty($arr['size']) ? "" : " btn-{$arr['size']}";
        $w = empty($arr['w']) ? "" : " w-{$arr['w']}";
        $class = empty($arr['class']) ? "btn$color$size$w" : $arr['class'];

        // Build data attributes
        $dataAttrs = array_filter($arr, function ($key) {
            return strpos($key, 'data-') === 0;
        }, ARRAY_FILTER_USE_KEY);

        $dataString = '';
        foreach ($dataAttrs as $key => $value) {
            $dataString .= " $key='$value'";
        }

        // Build aria attributes
        $ariaAttrs = array_filter($arr, function ($key) {
            return strpos($key, 'aria-') === 0;
        }, ARRAY_FILTER_USE_KEY);

        $ariaString = '';
        foreach ($ariaAttrs as $key => $value) {
            $ariaString .= " $key='$value'";
        }

        $disabled = empty($arr['disabled']) ? "" : " disabled";
        $active = empty($arr['active']) ? "" : " active";

        return "<button type='$type' 
                class='$class'
                $name
                $id
                $dataString
                $ariaString
                $active
                $disabled>$text</button>";
    }

    /**
     * Create a card component
     */
    public static function Card(...$attrs)
    {
        $arr = self::getAttr($attrs);
        $arr = self::sanitizeAttributes($arr);

        $class = $arr['class'] ?? "card";
        $title = $arr['title'] ?? "Card title";
        $image = $arr['image'] ?? "./public/ImageDefault.png";
        $width = $arr['w'] ?? "150";
        $height = $arr['h'] ?? "150";
        $content = $arr['body'] ?? "";

        $defaultContent = "<p class='card-text'>This is a wider card with supporting text below as a natural lead-in to 
            additional content. This content is a little bit longer.</p>
            <p class='card-text'><small class='text-body-secondary'>Last updated 3 mins ago</small></p>";

        $createContent = empty($content) ? $defaultContent : $content;

        return "
        <div class='$class'>
            <img src='$image' class='card-img-top' alt='Image-$title' width='$width' height='$height'>
            <div class='card-body'>
                <h5 class='card-title'>$title</h5>
                $createContent
            </div>
        </div>";
    }

    /**
     * Create a button group component
     */
    public static function ButtonGroup($datas = ["Left", "Middle", "Right"], ...$attrs)
    {
        $arr = self::getAttr($attrs);
        $arr = self::sanitizeAttributes($arr);

        $vertical = empty($arr['vertical']) ? "" : "-vertical";
        $color = $arr['color'] ?? "primary";
        $outline = $arr['outline'] ?? "";
        $class = $arr['class'] ?? "btn-group";
        $aria_label = $arr['aria-label'] ?? "Basic example";

        $html = "<div class='$class$vertical' role='group' aria-label='$aria_label'>";
        foreach ($datas as $text) {
            $html .= self::Button("text:$text", "color:$color", "outline:$outline");
        }
        $html .= "</div>";
        return $html;
    }

    /**
     * Create a carousel component
     */
    public static function Carousel($images = [], ...$attrs)
    {
        $arr = self::getAttr($attrs);
        $arr = self::sanitizeAttributes($arr);

        $id = $arr['id'] ?? "carouselExample";
        $selected = $arr['index'] ?? "0";

        $html = "<div id='$id' class='carousel slide'>";
        $html .= "<div class='carousel-indicators'>";

        // Create indicators
        foreach ($images as $index => $img) {
            $isactive = $index == (int)$selected;
            $strActive = $isactive ? "active" : "0";
            $strAria = $isactive ? "true" : "false";
            $html .= self::Button(
                "text:.",
                "data-bs-target:#$id",
                "data-bs-slide-to:$index",
                "class:$strActive",
                "aria-current:$strAria",
                "aria-label:Slide$index"
            );
        }

        $html .= "</div><div class='carousel-inner'>";

        // Create slides
        foreach ($images as $index => $img) {
            $isactive = $index == (int)$selected ? " active" : "";
            $html .= "<div class='carousel-item$isactive'>";
            $html .= self::Image($img, "alt:carousel$index", "class:d-block w-100", "h:800");
            $html .= "</div>";
        }

        $html .= "</div>";

        // Previous button
        $html .= self::Button(
            "class:carousel-control-prev",
            "data-bs-target:#$id",
            "data-bs-slide:prev",
            "text:<span class='carousel-control-prev-icon' aria-hidden='true'></span><span class='visually-hidden'>Previous</span>"
        );

        // Next button
        $html .= self::Button(
            "class:carousel-control-next",
            "data-bs-target:#$id",
            "data-bs-slide:next",
            "text:<span class='carousel-control-next-icon' aria-hidden='true'></span><span class='visually-hidden'>Next</span>"
        );

        $html .= "</div>";
        return $html;
    }

    /**
     * Create a dropdown component
     */
    public static function Dropdown($datas = [], ...$attrs)
    {
        $arr = self::getAttr($attrs);
        $arr = self::sanitizeAttributes($arr);

        $color = $arr['color'] ?? "primary";
        $text = $arr['text'] ?? "Dropdown button";
        $size = $arr['size'] ?? "";
        $select = $arr['index'] ?? "-1";

        $createData = empty($datas) ?
            ["Action" => "#", "Another action" => "#", "Something else here" => "#"] :
            $datas;

        $html = "<div class='dropdown'>";
        $html .= self::Button(
            "text:$text",
            "size:$size",
            "color:$color dropdown-toggle",
            "data-bs-toggle:dropdown",
            "aria-expanded:false"
        );

        $html .= "<ul class='dropdown-menu'>";
        foreach ($createData as $index => $link) {
            $isSelect = (int)$select == $index;
            $strActive = $isSelect ? " active" : "";
            $html .= "<li><a class='dropdown-item$strActive' href='$link'>$text</a></li>";
        }
        $html .= "</ul></div>";

        return $html;
    }

    /**
     * Create a modal component
     */
    public static function Modal(...$attrs)
    {
        $arr = self::getAttr($attrs);
        $arr = self::sanitizeAttributes($arr);

        $class = $arr['class'] ?? "fade";
        $scrollable = empty($arr['scrollable']) ? "" : "modal-dialog-scrollable";
        $id = $arr['id'] ?? "exampleModal";
        $position = $arr['position'] ?? "";
        $size = $arr['size'] ?? "";
        $title = $arr['title'] ?? "Modal title";
        $titleCenter = $arr['titlecenter'] ?? "";
        $content = $arr['content'] ?? "<p>Modal body text goes here.</p>";

        $createPosition = empty($position) ? "" : "modal-dialog-$position ";
        $createSize = empty($size) ? "" : "modal-$size";
        $createTitleCenter = empty($titleCenter) ? "" : "text-center";

        $footer = $arr['footer'] ??
            "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
             <button type='button' class='btn btn-primary'>Save changes</button>";

        return "
        <div class='modal $class' id='$id' tabindex='-1' aria-labelledby='$id' aria-hidden='true'>
            <div class='modal-dialog $scrollable $createPosition $createSize'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title $createTitleCenter w-100' id='{$id}Label'>$title</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body'>$content</div>
                    <div class='modal-footer d-flex justify-content-center'>$footer</div>
                </div>
            </div>
        </div>";
    }

    /**
     * Create a tab component
     */
    public static function Tab($datas = [], ...$attrs)
    {
        $arr = self::getAttr($attrs);
        $arr = self::sanitizeAttributes($arr);

        $view = empty($arr['view']) ? "" : " nav-{$arr['view']} ";
        $select = !isset($arr['index']) ? "-1" : (int)$arr['index'];
        $disabled = empty($arr['disabled']) ? "" : (int)$arr['disabled'];

        $defaultData = ["list1" => "#", "list2" => "#", "list3" => "#", "list4" => "#"];
        $createData = empty($datas) ? $defaultData : $datas;

        $html = "<ul class='nav$view'>";
        foreach ($createData as $index => $link) {
            $isActive = $index == $select;
            $isDisabled = $index == $disabled;

            if ($isActive) {
                $html .= "<li class='nav-item'><a class='nav-link active' aria-current='page' href='$link'>$index</a></li>";
            } else if ($isDisabled) {
                $html .= "<li class='nav-item'><a class='nav-link disabled' aria-disabled='true' href='$link'>$index</a></li>";
            } else {
                $html .= "<li class='nav-item'><a class='nav-link' href='$link'>$index</a></li>";
            }
        }
        $html .= "</ul>";

        return $html;
    }

    /**
     * Create an offcanvas component
     */
    public static function Offcanvas($body = "", ...$attrs)
    {
        $arr = self::getAttr($attrs);
        $arr = self::sanitizeAttributes($arr);

        $id = $arr['id'] ?? "staticBackdrop";
        $placement = $arr['placement'] ?? "start";
        $static = empty($arr['static']) ? "" : " data-bs-backdrop=static";
        $dark = empty($arr['dark']) ? "" : " text-bg-dark";
        $data = empty($body) ? "<div>I will not close if you click outside of me.</div>" : $body;

        return "
        <div class='offcanvas$dark offcanvas-$placement'$static tabindex='-1' id='$id' aria-labelledby='$id-Label'>
            <div class='offcanvas-header'>
                <h5 class='offcanvas-title' id='$id-Label'>Offcanvas</h5>
                " . self::Button("class:btn-close", "data-bs-dismiss:offcanvas", "aria-label:Close", "text:") . "
            </div>
            <div class='offcanvas-body'>$data</div>
        </div>";
    }

    /**
     * Create a pagination component
     */
    public static function Pagination($pages = 10, ...$attrs)
    {
        $arr = self::getAttr($attrs);
        $arr = self::sanitizeAttributes($arr);

        $index = empty($arr['index']) ? 0 : (int)$arr['index'];
        $size = empty($arr['size']) ? "" : " pagination-{$arr['size']}";

        $html = "<nav aria-label='Page navigation'><ul class='pagination$size'>";
        $html .= "<li class='page-item'><a class='page-link' href='#'>Previous</a></li>";

        for ($i = 0; $i < $pages; $i++) {
            $text = $i + 1;
            $isActive = $i == $index ? " active" : "";
            $html .= "<li class='page-item$isActive'><a class='page-link' href='#'>$text</a></li>";
        }

        $html .= "<li class='page-item'><a class='page-link' href='#'>Next</a></li>";
        $html .= "</ul></nav>";

        return $html;
    }

    /**
     * Create a progress bar component
     */
    public static function Progress($value = 0, ...$attrs)
    {
        $arr = self::getAttr($attrs);
        $arr = self::sanitizeAttributes($arr);

        $height = $arr['h'] ?? "18";
        $showtext = empty($arr['showtext']) ? "" : $value . "%";
        $color = empty($arr['color']) ? "" : " bg-{$arr['color']}";

        return "
        <div class='progress' role='progressbar' aria-label='Progress' aria-valuenow='$value' 
             aria-valuemin='0' aria-valuemax='100' style='height: {$height}px'>
            <div class='progress-bar$color' style='width: $value%'>$showtext</div>
        </div>";
    }

    /**
     * Create a spinner component
     */
    public static function Spinner(...$attrs)
    {
        $arr = self::getAttr($attrs);
        $arr = self::sanitizeAttributes($arr);

        $type = $arr['type'] ?? "border";
        $color = empty($arr['color']) ? "" : " text-{$arr['color']}";
        $size = empty($arr['size']) ? "" : " spinner-$type-{$arr['size']}";
        $text = $arr['text'] ?? "Loading...";

        return "
        <div class='d-flex align-items-center gap-1'>
            <span class='spinner-$type$color$size' aria-hidden='true'></span>
            <span class='$color' role='status'>$text</span>
        </div>";
    }

    /**
     * Create a toast component
     */
    public static function Toast(...$attrs)
    {
        $arr = self::getAttr($attrs);
        $arr = self::sanitizeAttributes($arr);

        $title = $arr['title'] ?? "Bootstrap";
        $time = $arr['time'] ?? "11 mins ago";
        $content = $arr['content'] ?? "Hello, world! This is a toast message.";

        return "
        <div class='toast-container position-fixed bottom-0 end-0 p-3'>
            <div id='liveToast' class='toast' role='alert' aria-live='assertive' aria-atomic='true'>
                <div class='toast-header'>
                    " . self::Image("", "class:rounded me-2", "w:20") . "
                    <strong class='me-auto'>$title</strong>
                    <small>$time</small>
                    " . self::Button("class:btn-close", "data-bs-dismiss:toast", "aria-label:Close", "text:") . "
                </div>
                <div class='toast-body'>$content</div>
            </div>
        </div>";
    }

    /**
     * Create a ratio component (usually for responsive embeds)
     */
    public static function Ratio($url = "", ...$attrs)
    {
        $arr = self::getAttr($attrs);
        $arr = self::sanitizeAttributes($arr);

        $ratio = empty($arr['ratio']) ? "ratio-16x9" : "ratio-{$arr['ratio']}";
        $createurl = empty($url) ? "https://www.youtube.com/embed/zpOULjyy-n8?rel=0" : $url;

        return "
        <div class='ratio $ratio'>
            <iframe src='$createurl' title='Embedded content' allowfullscreen></iframe>
        </div>";
    }

    /**
     * Create a label component
     */
    public static function Label(...$attrs)
    {
        $arr = self::getAttr($attrs);
        $arr = self::sanitizeAttributes($arr);

        $forname = empty($arr['for']) ? "" : " for='{$arr['for']}'";
        $text = $arr['text'] ?? "Label";
        $class = $arr['class'] ?? "form-label";

        return "<label$forname class='$class'>$text</label>";
    }

    /**
     * Create an input component - continued
     */
    public static function Input(...$attrs)
    {
        $arr = self::getAttr($attrs);
        $arr = self::sanitizeAttributes($arr);

        $type = $arr['type'] ?? "text";
        $id = $arr['id'] ?? "";
        $name = $arr['name'] ?? "";
        $hint = $arr['hint'] ?? "";
        $value = $arr['value'] ?? "";
        $align = $arr['align'] ?? "";
        $class = $arr['class'] ?? "form-control $align";
        $autocomplete = empty($arr['autocomplete']) ? "" : " autocomplete='{$arr['autocomplete']}'";
        $disabled = !empty($arr['disabled']) ? " disabled" : "";
        $readonly = !empty($arr['readonly']) ? " readonly" : "";
        $required = !empty($arr['required']) ? " required" : "";

        return "<input type='$type' class='$class' name='$name' id='$id' placeholder='$hint' 
                value='$value'$disabled$readonly$autocomplete$required>";
    }

    /**
     * Create a textarea component
     */
    public static function Textarea(...$attrs)
    {
        $arr = self::getAttr($attrs);
        $arr = self::sanitizeAttributes($arr);

        $id = $arr['id'] ?? "textareaid";
        $name = $arr['name'] ?? "";
        $rows = $arr['rows'] ?? "3";
        $hint = $arr['hint'] ?? "";
        $value = $arr['value'] ?? "";
        $class = $arr['class'] ?? "form-control";
        $disabled = !empty($arr['disabled']) ? " disabled" : "";
        $readonly = !empty($arr['readonly']) ? " readonly" : "";
        $required = !empty($arr['required']) ? " required" : "";

        return "<textarea class='$class' name='$name' id='$id' rows='$rows' 
                placeholder='$hint'$disabled$readonly$required>$value</textarea>";
    }

    /**
     * Create a select component
     */
    public static function Select($options = [], ...$attrs)
    {
        $arr = self::getAttr($attrs);
        $arr = self::sanitizeAttributes($arr);

        $id = $arr['id'] ?? "selectid";
        $name = $arr['name'] ?? "";
        $class = $arr['class'] ?? "form-select";
        $disabled = !empty($arr['disabled']) ? " disabled" : "";
        $required = !empty($arr['required']) ? " required" : "";

        $html = "<select class='$class' name='$name' id='$id'$disabled$required>";

        if (empty($options)) {
            $html .= "<option selected>Open this select menu</option>";
        } else {
            foreach ($options as $value => $text) {
                $selected = isset($arr['selected']) && $arr['selected'] == $value ? " selected" : "";
                $html .= "<option value='$value'$selected>$text</option>";
            }
        }

        $html .= "</select>";
        return $html;
    }

    /**
     * Create a checkbox component
     */
    public static function Checkbox(...$attrs)
    {
        $arr = self::getAttr($attrs);
        $arr = self::sanitizeAttributes($arr);

        $id = $arr['id'] ?? "checkboxid";
        $text = $arr['text'] ?? "Checkbox";
        $name = $arr['name'] ?? "";
        $value = $arr['value'] ?? "";
        $checked = !empty($arr['checked']) ? " checked" : "";
        $disabled = !empty($arr['disabled']) ? " disabled" : "";

        return "
        <div class='form-check'>
            <input class='form-check-input' type='checkbox' value='$value' name='$name' 
                   id='$id'$checked$disabled>
            <label class='form-check-label' for='$id'>$text</label>
        </div>";
    }

    /**
     * Create a radio component
     */
    public static function Radio(...$attrs)
    {
        $arr = self::getAttr($attrs);
        $arr = self::sanitizeAttributes($arr);

        $id = $arr['id'] ?? "radioid";
        $name = $arr['name'] ?? "";
        $value = $arr['value'] ?? "";
        $text = $arr['text'] ?? "Radio";
        $checked = !empty($arr['checked']) ? " checked" : "";
        $disabled = !empty($arr['disabled']) ? " disabled" : "";

        return "
        <div class='form-check'>
            <input class='form-check-input' type='radio' name='$name' id='$id' 
                   value='$value'$checked$disabled>
            <label class='form-check-label' for='$id'>$text</label>
        </div>";
    }

    /**
     * Create a switch component
     */
    public static function Switch(...$attrs)
    {
        $arr = self::getAttr($attrs);
        $arr = self::sanitizeAttributes($arr);

        $id = $arr['id'] ?? "switchid";
        $name = $arr['name'] ?? "";
        $value = $arr['value'] ?? "";
        $text = $arr['text'] ?? "Switch";
        $checked = !empty($arr['checked']) ? " checked" : "";
        $disabled = !empty($arr['disabled']) ? " disabled" : "";

        return "
        <div class='form-check form-switch'>
            <input class='form-check-input' type='checkbox' role='switch' id='$id' 
                   name='$name' value='$value'$checked$disabled>
            <label class='form-check-label' for='$id'>$text</label>
        </div>";
    }

    /**
     * Create an image component
     */
    public static function Image($src = "", ...$attrs)
    {
        $arr = self::getAttr($attrs);
        $arr = self::sanitizeAttributes($arr);

        $source = empty($src) ? "./public/Logo.png" : $src;
        $class = $arr['class'] ?? "img-fluid";
        $alt = $arr['alt'] ?? "image-default";
        $width = empty($arr['w']) ? "" : " width='{$arr['w']}'";
        $height = empty($arr['h']) ? "" : " height='{$arr['h']}'";

        return "<img src='$source' class='$class' alt='$alt'$width$height>";
    }

    /**
     * Utility function to determine if an attribute should be considered disabled
     */
    private static function isDisabled($attr)
    {
        return !empty($attr['disabled']) ? " disabled" : "";
    }

    /**
     * Utility function to determine if an attribute should be considered readonly
     */
    private static function isReadOnly($attr)
    {
        return !empty($attr['readonly']) ? " readonly" : "";
    }

    /**
     * Utility function to determine if an attribute is checked
     */
    private static function isChecked($attr)
    {
        return !empty($attr['checked']) ? " checked" : "";
    }

    /**
     * Utility function to get HTML title attribute
     */
    private static function getTitle($attr)
    {
        return !empty($attr['title']) ? " title='{$attr['title']}'" : "";
    }
}
