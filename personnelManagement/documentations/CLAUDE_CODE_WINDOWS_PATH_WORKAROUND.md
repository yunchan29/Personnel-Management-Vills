# Claude Code Windows Path Workaround

## Issue
There's a file modification bug in Claude Code when working on Windows systems.

## Workaround
**Always use complete absolute Windows paths with drive letters and backslashes for ALL file operations.**

### Path Format Rules
- ✅ Correct: `D:\Joel\Webpage\Personnel-Management-Vills\file.php`
- ❌ Incorrect: `d:/Joel/Webpage/Personnel-Management-Vills/file.php`
- ❌ Incorrect: `./file.php`
- ❌ Incorrect: `file.php`

### Applied To
This rule applies to ALL file operations including:
- Read
- Write
- Edit
- Glob
- Any other file manipulation tools

### Examples
```
Read: D:\Joel\Webpage\Personnel-Management-Vills\personnelManagement\resources\views\users\leaveForm.blade.php
Edit: D:\Joel\Webpage\Personnel-Management-Vills\personnelManagement\app\Http\Controllers\UserController.php
Write: D:\Joel\Webpage\Personnel-Management-Vills\personnelManagement\database\migrations\new_migration.php
```

## When to Apply
Apply this rule going forward for **every prompt**, not just for individual files.
