@echo off

rem -------------------------------------------------------------
rem  Leaps command line script for Windows.
rem
rem  This is the bootstrap script for running Leaps on Windows.
rem
rem  @author Tongle Xu <xutongle@gmail.com>
rem  @link http://www.tintsoft.com/
rem  @copyright Copyright &copy; 2008 Tint Software LLC
rem  @license http://www.tintsoft.com/html/about/license/
rem  @version $Id$
rem -------------------------------------------------------------

@setlocal

set LEAPS_PATH=%~dp0

if "%PHP_COMMAND%" == "" set PHP_COMMAND=php.exe

"%PHP_COMMAND%" "%LEAPS_PATH%leapsc" %*

@endlocal