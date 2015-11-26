<?php
namespace SeleniumSetup\System;

interface SystemInterface
{
    public function createDir($dirFullPath);
    public function isDir($dirFullPath);
    public function isFile($fileFullPath);
    public function createFile($fileFullPath, $contents = '');
    public function readFile($fileFullPath);
    public function openFileForReading($fileFullPath);
    public function readFileLineAsCsv($handler, $limit = 0, $separator = '|');
    public function execCommand($cmd);
}
