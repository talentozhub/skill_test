<?php
namespace App\Models;

use App\Utils\Database;
use PDO;
use Exception;

class Employee
{
    /**
     * The unique identifier
     * @var int
     */
    public $id;

    /**
     * The employee's full name
     * @var string
     */
    public $name;

    /**
     * The employee's phone number
     * @var string
     */
    public $phone_number;

    /**
     * The employee's password.
     * @var string
     */
    public $password;

    /**
     * The employee's email address
     * @var string
     */
    public $email;

    /**
     * @var string
     * @see self::TYPE_* constants
     */
    public $type;

    /**
     * @var bool
     */
    public $email_sent = 0;

    
    public $gender;

    /**
     * Full time employee - works 40 hours week+
     */
    const TYPE_FULL_TIME = "full-time";

    /**
     * Part time employee - works < 40 hours week.
     */
    const TYPE_PART_TIME = "part-time";

    const GENDER_MALE = "male";
    const GENDER_FEMALE = "female";
}
