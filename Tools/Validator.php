<?php

namespace Tools;

/**
 * Input validation utility class
 * Validates common data types (email, string, number, boolean) and tracks validation errors
 */
class Validator
{
    /**
     * Stores validation errors: [field_key => error_message]
     * @var array
     */
    protected $errors;

    /**
     * Validate an email address (checks format and non-empty value)
     * Depends on the string() method to first validate basic string rules
     *
     * @param array $data Input data array (e.g., $_POST, $_GET)
     * @param string $key Field key in the data array to validate
     * @return string|null Valid email address if validation passes, null if fails
     */
    public function email($data, $key)
    {
        // First validate the input as a string (ensures non-empty value via string() method)
        if (empty($val = $this->string($data, $key))) {
            return null;
        }

        // Check if the string matches a valid email format using PHP's built-in filter
        if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$key] = 'Invalid email format';
        }

        // Return the validated email (or null if string() already added an error)
        return $val;
    }

    /**
     * Validate a string (checks non-empty, minimum length, and maximum length)
     * Trims whitespace from the input before validation
     *
     * @param array $data Input data array (e.g., $_POST, $_GET)
     * @param string $key Field key in the data array to validate
     * @param int $min Minimum allowed length (default: 1, requires non-empty value)
     * @param int $max Maximum allowed length (default: 255)
     * @return string|null Valid trimmed string if validation passes, null if fails
     */
    public function string($data, $key, $min = 1, $max = 255)
    {
        // Get the value from the data array and trim leading/trailing whitespace
        $val = trim($data[$key]);

        // Calculate the length of the trimmed string
        $len = strlen($val);

        // Add error if the string is empty (after trimming)
        if ($len === 0) {
            $this->errors[$key] = "Please input $key";
        }
        // Add error if the string is shorter than the minimum length
        elseif ($len < $min) {
            $this->errors[$key] = "$key must be at least $min characters";
        }
        // Add error if the string is longer than the maximum length
        elseif ($len > $max) {
            $this->errors[$key] = "$key must be less than $max characters";
        }

        // Return null if any validation errors exist for this field
        if ($this->hasError()) {
            return null;
        }

        // Return the validated trimmed string
        return $val;
    }

    /**
     * Validate and convert a value to an integer
     * Returns a default value if the field is not present in the data array
     *
     * @param array $data Input data array (e.g., $_POST, $_GET)
     * @param string $key Field key in the data array to validate
     * @param int|null $default Value to return if the field is not set (default: null)
     * @return int|null Converted integer if field exists, default value otherwise
     */
    public function number($data, $key, $default = null)
    {
        // If the field exists in the data array, convert its value to an integer
        if (isset($data[$key])) {
            return (int)$data[$key];
        }
        // Return the default value if the field is not set
        return $default;
    }

    /**
     * Validate and convert a value to a boolean
     * Returns a default value if the field is not present in the data array
     *
     * @param array $data Input data array (e.g., $_POST, $_GET)
     * @param string $key Field key in the data array to validate
     * @param bool|null $default Value to return if the field is not set (default: null)
     * @return bool|null Converted boolean if field exists, default value otherwise
     */
    public function boolean($data, $key, $default = null)
    {
        // If the field exists in the data array, convert its value to a boolean
        if (isset($data[$key])) {
            return (bool)$data[$key];
        }
        // Return the default value if the field is not set
        return $default;
    }

    /**
     * Check if any validation errors have been recorded
     *
     * @return bool True if errors exist, false otherwise
     */
    public function hasError()
    {
        return !empty($this->errors);
    }

    /**
     * Retrieve all recorded validation errors
     *
     * @return array Validation errors as [field_key => error_message]
     */
    public function errors()
    {
        return $this->errors;
    }
}