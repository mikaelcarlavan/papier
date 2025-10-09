<?php

namespace Papier\Component;

use Papier\Component\Base\BaseComponent;
use Papier\File\FileBody;
use Papier\File\FileHeader;
use Papier\File\FileTrailer;

class TableComponent extends BaseComponent
{
	/**
	 * Header
	 *
	 * @var TableHeadComponent
	 */
	private TableHeadComponent $header;

	/**
	 * Body
	 *
	 * @var TableBodyComponent
	 */
	private TableBodyComponent $body;

	/**
	 * Footer
	 *
	 * @var TableFootComponent
	 */
	private TableFootComponent $footer;

	/**
	 * Create a new TableComponent instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->header = new TableHeadComponent();
		$this->footer = new TableFootComponent();
		$this->body = new TableBodyComponent();
	}

	/**
	 * Get header.
	 *
	 * @return TableHeadComponent
	 */
	public function getHeader(): TableHeadComponent
	{
		return $this->header;
	}

	/**
	 * Get body.
	 *
	 * @return TableBodyComponent
	 */
	public function getBody(): TableBodyComponent
	{
		return $this->body;
	}

	/**
	 * Get trailer.
	 *
	 * @return TableFootComponent
	 */
	public function getFooter(): TableFootComponent
	{
		return $this->footer;
	}

	function format(): TableComponent
	{
		// TODO: Implement format() method.
	}
}