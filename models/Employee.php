<?php

/**
 * Class Employee
 *
 * This model represents an employee in our system.
 */
class Employee {

    /**
     * @var bool
     */
    public $email_sent;

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
    public $phoneNumber;

    /**
     * The employee's gender
     * @var string
     */
    public $gender;

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
     * Full time employee - works 40 hours week+
     */
    const TYPE_FULL_TIME = "full-time";

    /**
     * Part time employee - works < 40 hours week.
     */
    const TYPE_PART_TIME = "part-time";
}