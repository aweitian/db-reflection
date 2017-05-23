<?php

namespace Tian;

interface ITableReflection {
	/**
	 * return array
	 */
	function getPk();
	/**
	 * @return array
	 */
	function getColumnNames();
	/**
	 * return string
	 */
	function getEngineType();
	/**
	 * return string
	 */
	function getComment();
}