<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace dataformat_sqlite;

use core\dataformat\base;

/**
 * SQLite Database format writer.
 *
 * @package   dataformat_sqlite
 * @copyright 2026 Felix Yeung
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class writer extends base {
    /** @var string */
    public $mimetype = 'application/vnd.sqlite3';

    /** @var string $extension */
    public $extension = '.sqlite';

    /**
     * {@inheritDoc}
     */
    public function write_record($record, $rownum) {
    }
}
