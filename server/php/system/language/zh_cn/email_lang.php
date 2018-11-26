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

$lang['email_must_be_array'] = 'The email validation method must be passed an array.';
$lang['email_invalid_address'] = '无效的邮箱地址: %s';
$lang['email_attachment_missing'] = '找不到邮件附件: %s';
$lang['email_attachment_unreadable'] = '无法打开邮件附件: %s';
$lang['email_no_from'] = '请配置from';
$lang['email_no_recipients'] = '请指定收件人: To, Cc, 或者 Bcc';
$lang['email_send_failure_phpmail'] = '无法用mail()函数发送邮件。你的服务器可能没有配置好';
$lang['email_send_failure_sendmail'] = '无法用Sendmail发送邮件。你的服务器可能没有配置好';
$lang['email_send_failure_smtp'] = '无法用SMTP发送邮件。你的服务器可能没有配置好';
$lang['email_sent'] = '邮件已成功发送，协议是 %s';
$lang['email_no_socket'] = '无法与Sendmail建立套接字，请检查配置';
$lang['email_no_hostname'] = '你没有配置SMTP的hostname.';
$lang['email_smtp_error'] = '发生了一个SMTP错误: %s';
$lang['email_no_smtp_unpw'] = '错误: 你必须配置好SMTP的用户名和密码';
$lang['email_failed_smtp_login'] = '无法发送AUTH LOGIN 指令. 错误: %s';
$lang['email_smtp_auth_un'] = 'Failed to authenticate username. Error: %s';
$lang['email_smtp_auth_pw'] = 'Failed to authenticate password. Error: %s';
$lang['email_smtp_data_failure'] = '无法发送数据: %s';
$lang['email_exit_status'] = '退出代码: %s';
