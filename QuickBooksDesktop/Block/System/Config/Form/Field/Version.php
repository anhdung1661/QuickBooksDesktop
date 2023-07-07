<?php

/**
 * Created by PhpStorm.
 * User: doanhcn2
 * Date: 31/01/2020
 * Time: 14:36
 */


namespace Magenest\QuickBooksDesktop\Block\System\Config\Form\Field;


use Magento\Backend\Block\Template;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Module\Dir\Reader as DirReader;

/**
 * Show module information in QuickBooks Desktop Settings
 * @package Magenest\QuickBooksDesktop\Block\System\Config\Form\Field
 */
class Version extends Template implements RendererInterface
{
    /**
     * @var DirReader
     */
    protected $dirReader;

    /**
     * @var DirectoryList
     */
    protected $directory_list;

    /**
     * Version constructor.
     * @param DirectoryList $directory_list
     * @param DirReader $dirReader
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        DirectoryList $directory_list,
        DirReader $dirReader,
        Template\Context $context,
        array $data = []
    )
    {
        $this->directory_list = $directory_list;
        $this->dirReader = $dirReader;
        parent::__construct($context, $data);
    }

    /**
     * Render form element as HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = '';
        if ($element->getData('group')['id'] == 'qbd_version') {
            $html = $this->toHtml();
        }
        return $html;
    }

    /**
     * @return mixed|string
     */
    public function getVersion()
    {
        $installVersion = "unidentified";
        $composer = $this->getComposerInformation("Magenest_QuickBooksDesktop");

        if ($composer) {
            $installVersion = $composer['version'];
        }

        return $installVersion;
    }

    /**
     * @param $moduleName
     * @return false|mixed
     */
    public function getComposerInformation($moduleName)
    {
        $dir = $this->dirReader->getModuleDir("", $moduleName);

        if (file_exists($dir.'/composer.json')) {
            return json_decode(file_get_contents($dir.'/composer.json'), true);
        }

        return false;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'Magenest_QuickBooksDesktop::system/config/form/field/version.phtml';
    }

    /**
     * @return string
     */
    public function getDownloadDebugUrl()
    {
        return $this->getUrl('qbdesktop/config/downloadDebug', ['version'=>$this->getVersion()]);
    }

    /**
     * @return string
     */
    public function getDebugFilePath() {
        try {
            return $this->directory_list->getPath("var") . "/log/quickbooks.log";
        } catch (FileSystemException $e) {
        }
        return '';
    }
}