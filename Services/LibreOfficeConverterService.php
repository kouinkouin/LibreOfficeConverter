<?php

namespace pxCore\LibreOfficeConverterBundle\Services;

/**
 * LibreOfficeConverterService
 *
 * @author Safwen Toukabri <safwen.toukabri@proxym-it.com>
 */
class LibreOfficeConverterService
{

    private $container;
    private $libreoffice;

    public function __construct($container)
    {
        $this->container = $container;
        $this->libreoffice = $container->getParameter('libreoffice');
        if ( ! isset($this->libreoffice)) {
            throw new \Exception('The parameter "libreoffice" is required');
        }

        if ( ! file_exists($this->libreoffice)) {
            throw new \Exception($this->libreoffice.': No such file or directory');
        }
    }

    public function convert($inFile, $outDir, $toFormat)
    {
        if ( ! file_exists($inFile)) {
            throw new \Exception($inFile.': No such file or directory');
        }

        if ( ! file_exists($outDir)) {
            throw new \Exception($outDir.': No such file or directory');
        }

        if ( ! is_readable($inFile)) {
            throw new \Exception($inFile.': The file is not readable');
        }
        if ( ! is_writable($outDir)) {
            throw new \Exception($outDir.': The file is not writable');
        }

        set_time_limit(0);

        // Génération de fichier (.pdf)
        $commandLine = $this->libreoffice.' --headless --convert-to '.$toFormat.' --outdir '.$outDir.' '.$inFile;

        exec($commandLine, $output, $err);
        if ($err) {
            throw new \Exception('Error: Cannot convert '.$inFile.' to '.$toFormat);
        }

        $inFileToArray = explode("/", $inFile);
        $inName = end($inFileToArray);

        $outExtension = explode(':', $toFormat)[0];

        $outName = implode('.', explode(".", $inName, -1)).'.'.$outExtension;
        chmod($outDir.'/'.$outName, 0755);
    }

}
