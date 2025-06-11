# CamelotPHP

A PHP wrapper for the [Camelot](https://camelot-py.readthedocs.io/) PDF table extraction tool. This package allows you to extract tables from PDF files using Camelot CLI, and return the results as arrays, objects, or JSON.

---

## 🚀 Features

- Extract tables from PDFs using Camelot CLI
- Structured page/table output as PHP array/object
- Automatically handles temp files and directories
- Detailed error handling for:
    - Missing Camelot
    - Missing Ghostscript
    - Invalid input file
- Supports both `lattice` and `stream` parsing modes

---
## Requirements

- **PHP 8.0+**  
  This library requires PHP version 8.0 or higher.

- **Camelot CLI**  
  You must install [Camelot](https://camelot-py.readthedocs.io/) (Python) on your system.  
  Make sure the `camelot` command is accessible in your `$PATH`.

- **Ghostscript**  
  Camelot depends on [Ghostscript](https://www.ghostscript.com/) for parsing PDF content.  
  Ensure `gs` is installed and available in your system `$PATH`.

- **PHP Extensions**
  - `ext-json` (for JSON encoding/decoding)

> 💡 **Tip**: Run `which camelot` and `which gs` to confirm both binaries are installed and accessible.
---

## 📦 Installation

```bash
composer require metolabs/camelot-php
```

> ⚠️ Make sure [`camelot`](https://camelot-py.readthedocs.io/en/master/user/install.html) and its dependencies like `ghostscript` are installed on your system and available in your `$PATH`.

---

## 🧪 Example Usage

```php
use MetoLabs\CamelotPHP\Camelot;

$camelot = Camelot::make('/path/to/file.pdf', 'lattice');

$result = $camelot->extract('array');

// Output example:
print_r($result['pages'][0]['tables'][1]);
```

You can also extract in other formats:

```php
$camelot->extract('string'); // returns JSON string
$camelot->extract('object'); // returns stdClass object
```

---

## 🧰 Configuration

You can optionally pass:
- Output path (defaults to a temporary directory)
- Custom binary path to `camelot`
- Debug mode (to print command + outputs)

```php
$camelot = Camelot::make('/file.pdf', 'stream', null, '/usr/local/bin/camelot', true);
```

---

## ❗ Exceptions

This package throws descriptive exceptions for common issues:

- `CamelotNotInstalledException`
- `FileNotFoundException`
- `DependencyErrorException`
- `CamelotExecutionException`

---

## 📄 License

MIT License

Copyright (c) 2025 MetoLabs

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the “Software”), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

**THE SOFTWARE IS PROVIDED “AS IS”**, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

---

## 🤝 Contributing

Feel free to submit pull requests or open issues if you find bugs or want improvements.
