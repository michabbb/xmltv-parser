<?php

namespace macropage\xmltv\parser;


use SimpleXMLElement;
use XMLReader;

class parser {

	private $file;
	private $channels;

	public function __construct() { }

	public function parse() {

		if (!$this->file) {
			die("Please specify xml file to parse.\n");
		}

		$xml = new XMLReader();
		//compress.zlib://'
		$xml->open($this->file);

		/** @noinspection PhpStatementHasEmptyBodyInspection */
		while ($xml->read() && $xml->name !== 'channel') {
			;
		}

		while ($xml->name === 'channel') {
			$element = new SimpleXMLElement($xml->readOuterXML());

			/** @noinspection PhpUndefinedFieldInspection */
			$this->channels[(string)$element->attributes()->id]=(string)$element->{'display-name'};

			$xml->next('channel');
			unset($element);
		}

		$xml->close();
	}

	/**
	 * @param mixed $file
	 */
	public function setFile($file) {
		$this->file = $file;
	}

	/**
	 * @return mixed
	 */
	public function getChannels() {
		return $this->channels;
	}

}