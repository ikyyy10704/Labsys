<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Pagination extends CI_Pagination {

    /**
     * Initialize Pagination
     *
     * @param	array	$params	Initialization parameters
     * @return	void
     */
    public function initialize($params = array())
    {
        // Fix for PHP 8.2 compatibility
        if (isset($params['cur_page'])) {
            $this->cur_page = $params['cur_page'];
            unset($params['cur_page']);
        }

        parent::initialize($params);
    }

    /**
     * Generate the pagination links
     *
     * @return	string
     */
    public function create_links()
    {
        // Fix for PHP 8.2 - ensure cur_page is never null
        if ($this->cur_page === null) {
            $this->cur_page = 0;
        }

        // If our item count or per-page total is zero there is no need to continue.
        if ($this->total_rows == 0 OR $this->per_page == 0)
        {
            return '';
        }

        // Calculate the total number of pages
        $num_pages = (int) ceil($this->total_rows / $this->per_page);

        // Is there only one page? Hm? Nothing to do here then.
        if ($num_pages === 1)
        {
            return '';
        }

        // Fix for PHP 8.2 - Check that cur_page is valid
        $this->cur_page = (int) $this->cur_page;

        if ( ! is_numeric($this->cur_page) || $this->cur_page < 0)
        {
            $this->cur_page = 0;
        }

        if ($this->cur_page > $this->total_rows)
        {
            $this->cur_page = ($num_pages - 1) * $this->per_page;
        }

        $uri_page_number = $this->cur_page;

        $this->num_links = (int) $this->num_links;

        if ($this->num_links < 1)
        {
            show_error('Your number of links must be a positive number.');
        }

        if ( ! is_null($this->uri_segment))
        {
            $this->cur_page = (int) $this->cur_page;
        }
        else
        {
            // Fix for PHP 8.2 - ensure page number is valid
            $this->cur_page = is_numeric($uri_page_number) ? (int) $uri_page_number : 0;
        }

        // Prepare values for parent method
        $this->cur_page = (int) $this->cur_page;

        return parent::create_links();
    }
}