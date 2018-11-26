<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2015, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	http://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['db_invalid_connection_str'] = '请检查配置的连接字符串';
$lang['db_unable_to_connect'] = '无法根据配置连接到数据库';
$lang['db_unable_to_select'] = '无法连接到指定的数据库 %s';
$lang['db_unable_to_create'] = '无法创建指定的数据库 %s';
$lang['db_invalid_query'] = '输入的语句非法';
$lang['db_must_set_table'] = '必须要指定数据表';
$lang['db_must_use_set'] = '你必须要用 "set"方法来更新一个实体';
$lang['db_must_use_index'] = '在更新多条数据时，你必须要指定索引';
$lang['db_batch_missing_index'] = 'One or more rows submitted for batch updating is missing the specified index.';
$lang['db_must_use_where'] = '更新时必须指定限制条件';
$lang['db_del_must_use_where'] = '删除时必须指定限制条件';
$lang['db_field_param_missing'] = 'To fetch fields requires the name of the table as a parameter.';
$lang['db_unsupported_function'] = '使用的数据库不支持此特性';
$lang['db_transaction_failure'] = '数据库迁移失败，已回滚操作';
$lang['db_unable_to_drop'] = '无法删除此数据库';
$lang['db_unsupported_feature'] = '使用的数据库不支持此特性';
$lang['db_unsupported_compression'] = '服务器不支持选定的文件压缩方式';
$lang['db_filepath_error'] = 'Unable to write data to the file path you have submitted.';
$lang['db_invalid_cache_path'] = '数据库缓冲文件路径不存在或不可写入';
$lang['db_table_name_required'] = '此操作需要指定表名';
$lang['db_column_name_required'] = '此操作需要一个字段名';
$lang['db_column_definition_required'] = 'A column definition is required for that operation.';
$lang['db_unable_to_set_charset'] = '连接时无法设定字符集 %s';
$lang['db_error_heading'] = '执行数据库语句时发生错误';
