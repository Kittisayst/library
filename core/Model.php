<?php
class Model
{
    /**
     * @var Database
     */
    protected $db;

    /**
     * @var string ຊື່ຕາຕະລາງ
     */
    protected $table;

    /**
     * @var string Primary key
     */
    protected $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    protected function sanitizeData($data)
    {
        $clean = [];
        foreach ($data as $key => $value) {
            // ກວດສອບ field names
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $key)) {
                throw new InvalidArgumentException("Invalid field name: $key");
            }
            // ທຳຄວາມສະອາດຄ່າ
            $clean[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        return $clean;
    }

    /**
     * ດຶງຂໍ້ມູນທັງໝົດ
     * @param array $conditions
     * @return array
     */
    public function all($conditions = [])
    {
        $sql = "SELECT * FROM {$this->table}";

        if (!empty($conditions)) {
            $sql .= " WHERE " . $this->buildWhereClause($conditions);
            return $this->db->query($sql, array_values($conditions))->fetchAll();
        }

        return $this->db->query($sql)->fetchAll();
    }

    /**
     * ດຶງຂໍ້ມູນແຖວດຽວຕາມ ID
     * @param int $id
     * @return object|false
     */
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->query($sql, [$id])->fetch();
    }

    /**
     * ດຶງຂໍ້ມູນແຖວດຽວຕາມເງື່ອນໄຂ
     * @param array $conditions
     * @return object|false
     */
    public function findOne($conditions)
    {
        $sql = "SELECT * FROM {$this->table} WHERE " . $this->buildWhereClause($conditions);
        return $this->db->query($sql, array_values($conditions))->fetch();
    }

    public function where($conditions, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE " . $conditions;
        return $this->db->query($sql, $value)->fetchAll();
    }

    /**
     * ບັນທຶກຂໍ້ມູນໃໝ່
     * @param array $data
     * @return int|false
     */
    public function insert($data)
    {
        $fields = array_keys($data);
        $values = array_fill(0, count($fields), '?');

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $values) . ")";

        $this->db->query($sql, array_values($data));
        return $this->db->lastInsertId();
    }

    /**
     * ອັບເດດຂໍ້ມູນ
     * @param int $id
     * @param array $data
     * @return int
     */
    public function update($id, $data)
    {
        $fields = array_keys($data);
        $set = implode(' = ?, ', $fields) . ' = ?';

        $sql = "UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = ?";

        $values = array_values($data);
        $values[] = $id;

        return $this->db->query($sql, $values)->rowCount();
    }

    /**
     * ລຶບຂໍ້ມູນ
     * @param int $id
     * @return int
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->query($sql, [$id])->rowCount();
    }

    public function beginTransaction()
    {
        $this->db->beginTransaction();
    }

    public function commit()
    {
        $this->db->commit();
    }

    public function rollback()
    {
        $this->db->rollback();
    }

    /**
     * ສ້າງ WHERE clause
     * @param array $conditions
     * @return string
     */
    protected function buildWhereClause($conditions)
    {
        return implode(' = ? AND ', array_keys($conditions)) . ' = ?';
    }

    /**
     * ຄົ້ນຫາໂດຍໃຊ້ LIKE
     * @param string $field
     * @param string $value
     * @return array
     */
    public function search($field, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$field} LIKE ?";
        return $this->db->query($sql, ["%$value%"])->fetchAll();
    }

    /**
     * ນັບຈຳນວນແຖວ
     * @param array $conditions
     * @return int
     */
    public function count($conditions = [])
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";

        if (!empty($conditions)) {
            $sql .= " WHERE " . $this->buildWhereClause($conditions);
            $result = $this->db->query($sql, array_values($conditions))->fetch();
        } else {
            $result = $this->db->query($sql)->fetch();
        }

        return $result->total;
    }

    /**
     * ດຶງຂໍ້ມູນແບບແບ່ງໜ້າ
     * @param int $page
     * @param int $perPage
     * @param array $conditions
     * @return array
     */
    public function paginate($page = 1, $perPage = 10, $conditions = [])
    {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM {$this->table}";

        if (!empty($conditions)) {
            $sql .= " WHERE " . $this->buildWhereClause($conditions);
        }

        $sql .= " LIMIT ? OFFSET ?";
        $values = empty($conditions) ? [] : array_values($conditions);
        $values[] = $perPage;
        $values[] = $offset;

        return [
            'data' => $this->db->query($sql, $values)->fetchAll(),
            'total' => $this->count($conditions),
            'page' => $page,
            'per_page' => $perPage
        ];
    }
}
