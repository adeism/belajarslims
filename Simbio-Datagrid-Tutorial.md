# Simbio Datagrid Tutorial

A simple tutorial on how to use the Simbio Datagrid class for creating dynamic data tables in PHP applications.

## Installation

1. First, make sure you have the required file in your project:
```php
require_once('simbio_datagrid.php');
```

2. Make sure you have a working database connection using MySQL/MariaDB.

## Basic Usage

### Simple Table Display
```php
<?php
// Create database connection
$db = new mysqli('localhost', 'username', 'password', 'database');

// Create new datagrid object
$datagrid = new simbio_datagrid();

// Set the table columns you want to display
$datagrid->setSQLColumn('id', 'name AS Name', 'email AS Email', 'phone AS Phone');

// Create the datagrid
echo $datagrid->createDataGrid($db, 'users', 20);
// Parameters: database object, table name, records per page
?>
```

### Editable Table with Checkbox
```php
<?php
$datagrid = new simbio_datagrid();

// Make the grid editable
$datagrid->edit_property = array('itemID', 'Edit'); // Enable edit button
$datagrid->chbox_property = array('itemID', 'Delete'); // Enable delete checkbox
$datagrid->chbox_form_URL = 'process.php'; // Form submission URL

// Set columns
$datagrid->setSQLColumn(
    'id AS ID',
    'name AS Name',
    'email AS Email',
    'created_at AS `Created Date`'
);

// Add WHERE clause if needed
$datagrid->setSQLCriteria('active = 1');

// Display the grid
echo $datagrid->createDataGrid($db, 'users', 20, true);
// Note: last parameter (true) makes the grid editable
?>
```

### Custom Column Content
```php
<?php
$datagrid = new simbio_datagrid();
$datagrid->setSQLColumn(
    'id AS ID',
    'name AS Name',
    'status AS Status',
    'created_at AS `Created Date`'
);

// Modify the Status column (column index 2) to show custom content
$datagrid->modifyColumnContent(2, 'callback{statusLabel}');

// Create the callback function
function statusLabel($db, $data, $col_idx) {
    $status = $data[$col_idx];
    switch($status) {
        case 1:
            return '<span class="badge bg-success">Active</span>';
        case 0:
            return '<span class="badge bg-danger">Inactive</span>';
        default:
            return '<span class="badge bg-secondary">Unknown</span>';
    }
}

echo $datagrid->createDataGrid($db, 'users', 20);
?>
```

### With Custom Sorting and Column Width
```php
<?php
$datagrid = new simbio_datagrid();

// Set column widths (in percentage or pixels)
$datagrid->column_width = array('10%', '30%', '30%', '30%');

// Disable sorting for specific columns (zero-based index)
$datagrid->disableSort(0, 3);

// Set default ordering
$datagrid->setSQLorder('name ASC');

$datagrid->setSQLColumn(
    'id AS ID',
    'name AS Name',
    'email AS Email',
    'created_at AS `Created Date`'
);

echo $datagrid->createDataGrid($db, 'users', 20);
?>
```

### AJAX Support
```php
<?php
$datagrid = new simbio_datagrid();

// Enable or disable AJAX (enabled by default)
$datagrid->using_AJAX = true;

// Configure other properties as needed
$datagrid->setSQLColumn(/*...*/);

echo $datagrid->createDataGrid($db, 'users', 20);
?>
```

## Advanced Features

### Custom Delete Action
```php
<?php
$datagrid = new simbio_datagrid();

// Change delete button text
$datagrid->chbox_action_button = 'Archive Selected';

// Custom confirmation message
$datagrid->chbox_confirm_msg = 'Are you sure you want to archive these items?';

// Other configurations...
echo $datagrid->createDataGrid($db, 'users', 20, true);
?>
```

### Hide Specific Columns
```php
<?php
$datagrid = new simbio_datagrid();

// Set columns including hidden ones
$datagrid->setSQLColumn(
    'id AS ID',
    'password AS Password',
    'name AS Name',
    'email AS Email'
);

// Hide columns by index (zero-based)
$datagrid->invisible_fields = array(1); // Hides the Password column

echo $datagrid->createDataGrid($db, 'users', 20);
?>
```

### Disable Paging
```php
<?php
$datagrid = new simbio_datagrid();

// Disable pagination
$datagrid->disable_paging = true;

// Show all records
echo $datagrid->createDataGrid($db, 'users', 999999);
?>
```

## Styling

The datagrid generates HTML tables that you can style with CSS. Example CSS:

```css
.datagrid {
    width: 100%;
    border-collapse: collapse;
}

.datagrid th {
    background-color: #f8f9fa;
    padding: 10px;
}

.alterCell {
    background-color: #ffffff;
}

.alterCell2 {
    background-color: #f8f9fa;
}

.datagrid td {
    padding: 8px;
    border: 1px solid #dee2e6;
}

.editLink {
    display: inline-block;
    padding: 2px 8px;
    background: #007bff;
    color: white;
    border-radius: 3px;
    text-decoration: none;
}
```

## Handling Form Submission

When using checkboxes for delete/archive operations, create a process file (e.g., `process.php`):

```php
<?php
// process.php
if (isset($_POST['itemID']) && is_array($_POST['itemID'])) {
    $db = new mysqli('localhost', 'username', 'password', 'database');
    
    foreach ($_POST['itemID'] as $id) {
        $id = (int)$id;
        // Perform your delete/archive operation
        $db->query("DELETE FROM users WHERE id = $id");
        // Or for soft delete:
        // $db->query("UPDATE users SET deleted_at = NOW() WHERE id = $id");
    }
    
    // Redirect back
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
?>
```

## Debug Mode

```php
<?php
$datagrid = new simbio_datagrid();

// Enable debug mode to see SQL queries
$datagrid->debug = true;

// Rest of your configuration...
?>
```

Remember to:
- Always sanitize your database inputs
- Use appropriate error handling
- Set up proper access control for edit/delete operations
- Test thoroughly in your development environment before deploying to production
