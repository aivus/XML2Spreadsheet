<?php

namespace aivus\XML2Spreadsheet\Converter;

interface ConverterInterface
{
    /**
     * @todo Method should receive file descriptor or SplFileObject
     */
    public function convert(/*resource*/ $file): void;
}
