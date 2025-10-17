<?php
require_once(__DIR__ . '/../config.php');

/**
 * Class Employee
 *
 * This model represents an employee in our system.
 */
class Employee {
    
    const TYPE_FULL_TIME = "full-time";
    const TYPE_PART_TIME = "part-time";
    
    const GENDER_MALE = "male";
    const GENDER_FEMALE = "female";

    public $id;
    public $name;
    public $phoneNumber;
    public $password;
    public $email;
    public $type;
    public $gender;
    public $email_sent = 0;

    /**
     * Save method - handles both insert and update operations
     *
     * @return void
     * @throws Exception if we had a problem saving
     */
    public function save() {
        $pdo = getDBConnection();
        
        try {
            if (!$this->id) {
                // INSERT new employee
                $stmt = $pdo->prepare("
                    INSERT INTO employee 
                    (`name`, `phone_number`, `type`, `gender`, `email`, `password`, `email_sent`) 
                    VALUES (:name, :phone, :type, :gender, :email, :password, :email_sent)
                ");
            } else {
                // UPDATE existing employee
                $stmt = $pdo->prepare("
                    UPDATE employee 
                    SET `name` = :name, 
                        `phone_number` = :phone, 
                        `type` = :type, 
                        `gender` = :gender, 
                        `email` = :email, 
                        `password` = :password, 
                        `email_sent` = :email_sent 
                    WHERE id = :id
                ");
                $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            }

            $stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $this->phoneNumber, PDO::PARAM_STR);
            $stmt->bindParam(':type', $this->type, PDO::PARAM_STR);
            $stmt->bindParam(':gender', $this->gender, PDO::PARAM_STR);
            $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $this->password, PDO::PARAM_STR);
            $stmt->bindParam(':email_sent', $this->email_sent, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                throw new Exception("Could not save employee.");
            }

            if (!$this->id) {
                $this->id = $pdo->lastInsertId();
            }
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    /**
     * Get employee by ID
     *
     * @param int $id
     * @return Employee|null
     * @throws Exception
     */
    public static function getById($id) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM employee WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch();
        if ($result) {
            return self::hydrate($result);
        }
        return null;
    }

    /**
     * Get all employees
     *
     * @return array
     * @throws Exception
     */
    public static function getAll() {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT * FROM employee");
        $results = $stmt->fetchAll();
        
        $employees = array();
        foreach ($results as $row) {
            $employees[] = self::hydrate($row);
        }
        return $employees;
    }

    /**
     * Hydrate employee object from database row
     *
     * @param array $data
     * @return Employee
     */
    private static function hydrate($data) {
        $employee = new self();
        $employee->id = $data['id'];
        $employee->name = $data['name'];
        $employee->phoneNumber = $data['phone_number'];
        $employee->password = $data['password'];
        $employee->email = $data['email'];
        $employee->type = $data['type'];
        $employee->gender = isset($data['gender']) ? $data['gender'] : null;
        $employee->email_sent = $data['email_sent'];
        return $employee;
    }

    /**
     * Convert employee to array for CSV export
     *
     * @return array
     */
    public function toArray() {
        return array(
            $this->id,
            $this->name,
            $this->email
        );
    }
}