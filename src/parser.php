<?php

namespace macropage\xmltv\parser;


use SimpleXMLElement;
use XMLReader;

class parser {

	private $file;
	private $channels;
	private $epgdata;
	private $channelfilter = [];
	private $offset        = 'P0Y0DT1H0M0S';

	public function __construct() { }

	/**
	 * @throws \RuntimeException
	 * @throws \Exception
	 */
	public function parse(): void {

		if (!$this->file) {
			throw new \RuntimeException('missing file: please use setFile before parse');
		}

		if (!file_exists($this->file)) {
			throw new \RuntimeException('file does not exists: ' . $this->file);
		}

		$xml = new XMLReader();
		//compress.zlib://'
		$xml->open($this->file);

		/** @noinspection PhpStatementHasEmptyBodyInspection */
		/** @noinspection LoopWhichDoesNotLoopInspection */
		/** @noinspection MissingOrEmptyGroupStatementInspection */
		while ($xml->read() && $xml->name !== 'channel') {
		}

		while ($xml->name === 'channel') {
			$element = new SimpleXMLElement($xml->readOuterXML());

			/** @noinspection PhpUndefinedFieldInspection */
			$this->channels[(string)$element->attributes()->id] = (string)$element->{'display-name'};

			$xml->next('channel');
			unset($element);
		}

		$xml->close();
		$xml->open($this->file);

		/** @noinspection PhpStatementHasEmptyBodyInspection */
		/** @noinspection LoopWhichDoesNotLoopInspection */
		/** @noinspection MissingOrEmptyGroupStatementInspection */
		while ($xml->read() && $xml->name !== 'programme') {
		}

		while ($xml->name === 'programme') {
			$element = new SimpleXMLElement($xml->readOuterXML());

			/** @noinspection PhpUndefinedFieldInspection */
			if (
				!\count($this->channelfilter)
				||
				(\count($this->channelfilter) && $this->channelMatchFilter((string)$element->attributes()->channel))
			) {

				/** @noinspection PhpUndefinedFieldInspection */
				$start         = str_replace(' +0000', '', (string)$element->attributes()->start);
				$start         = \DateTime::createFromFormat('YmdHis', $start)->add(new \DateInterval($this->offset))->format('Y-m-d H:i:s');
				$startDateTime = new \DateTime($start);

				/** @noinspection PhpUndefinedFieldInspection */
				$stop = str_replace(' +0000', '', (string)$element->attributes()->stop);
				$stop = \DateTime::createFromFormat('YmdHis', $stop)->add(new \DateInterval($this->offset))->format('Y-m-d H:i:s');

				/** @noinspection PhpUndefinedFieldInspection */
				$this->epgdata[(string)$element->attributes()->channel . ' ' . $startDateTime->format('YmdHis')] = [
					'start'       => $start,
					'start_raw'   => (string)$element->attributes()->start,
					'stop'        => $stop,
					'title'       => (string)$element->title,
					'sub-title'   => (string)$element->{'sub-title'},
					'desc'        => (string)$element->desc,
					'date'        => (string)$element->date,
					'country'     => (string)$element->country,
					'episode-num' => (string)$element->{'episode-num'},
				];

			}

			$xml->next('programme');
			unset($element);
		}

		$xml->close();

	}

	private function channelMatchFilter(string $channel): bool {
		return array_key_exists($channel, $this->channelfilter);
	}

	/**
	 * @param mixed $file
	 */
	public function setFile($file): void {
		$this->file = $file;
	}

	/**
	 * @return mixed
	 */
	public function getChannels() {
		return $this->channels;
	}

	/**
	 * @return mixed
	 */
	public function getEpgdata() {
		return $this->epgdata;
	}

	/**
	 * @param mixed $channelfilter
	 */
	public function setChannelfilter($channelfilter): void {
		$this->channelfilter[$channelfilter] = 1;
	}

	public function resetChannelfilter(): void {
		$this->channelfilter = [];
	}

	/**
	 * @param mixed $offset
	 */
	public function setOffset($offset): void {
		$this->offset = $offset;
	}


}