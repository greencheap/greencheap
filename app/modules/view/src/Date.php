<?php 
namespace GreenCheap\View;

use GreenCheap\Application as App;

class Date
{
    /**
     * @var \DateTime
     */
    protected \DateTime $date;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->date = new \DateTime('', new \DateTimeZone($this->getintl()));
    }

    /**
     * @return string
     */
    protected function getintl()
    {
       $intl = App::module('system/intl');
       $format = $intl->getFormats($intl->getLocale());
       return $format['TIMEZONE'];
    }

    /**
     * @return object
     */
    public function get()
    {
        return $this->date;
    }
}
?>