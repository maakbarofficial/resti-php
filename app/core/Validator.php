<?php

namespace App\Core;

class Validator
{
  private $errors = [];

  /**
   * Validate an array of data against rules
   * @param array $data Input data (e.g., $request->body)
   * @param array $rules Validation rules
   * @return bool True if valid, false if errors
   */
  public function validate(array $data, array $rules)
  {
    $this->errors = [];

    foreach ($rules as $field => $fieldRules) {
      $value = $data[$field] ?? null;

      foreach ($fieldRules as $rule) {
        $this->applyRule($field, $value, $rule);
      }
    }

    return empty($this->errors);
  }

  /**
   * Get validation errors
   * @return array
   */
  public function getErrors()
  {
    return $this->errors;
  }

  /**
   * Apply a single validation rule
   * @param string $field Field name
   * @param mixed $value Field value
   * @param string $rule Rule (e.g., required, email, min:6)
   */
  private function applyRule($field, $value, $rule)
  {
    if (strpos($rule, ':') !== false) {
      list($ruleName, $param) = explode(':', $rule);
    } else {
      $ruleName = $rule;
      $param = null;
    }

    switch ($ruleName) {
      case 'required':
        if ($value === null || $value === '') {
          $this->errors[$field][] = "$field is required";
        }
        break;
      case 'email':
        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
          $this->errors[$field][] = "$field must be a valid email";
        }
        break;
      case 'min':
        if ($value && strlen($value) < $param) {
          $this->errors[$field][] = "$field must be at least $param characters";
        }
        break;
      case 'max':
        if ($value && strlen($value) > $param) {
          $this->errors[$field][] = "$field must not exceed $param characters";
        }
        break;
      case 'in':
        $options = explode(',', $param);
        if ($value && !in_array($value, $options)) {
          $this->errors[$field][] = "$field must be one of: " . implode(', ', $options);
        }
        break;
      case 'integer':
        if ($value && (!filter_var($value, FILTER_VALIDATE_INT) || $value <= 0)) {
          $this->errors[$field][] = "$field must be a positive integer";
        }
        break;
      case 'boolean':
        if ($value !== null && !is_bool(filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE))) {
          $this->errors[$field][] = "$field must be a boolean";
        }
        break;
    }
  }
}
