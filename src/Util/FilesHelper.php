<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Util;

class FilesHelper
{
	/**
	 * Get the raw file data from $_FILES or an empty array if file data empty.
	 *
	 * @param string $file_name The file data that you want.
	 * @return array<mixed,mixed|array<string,mixed>> Single|Multi upload array data.
	 */
	public static function getRawFileData(string $file_name): array
	{
		return (self::isFileDataEmpty($_FILES[$file_name] ?? []) ? [] : $_FILES[$file_name]);
	}

	/**
	 * Check if the upload is empty.
	 *
	 * @param array<mixed,mixed|array<string,mixed>> $file_data The upload data to check. Either multi or single upload.
	 * @return boolean
	 */
	public static function isFileDataEmpty(array $file_data): bool
	{
		if ($file_data === [])
		{
			return true;
		}

		// Formatted multi-file upload
		if (array_key_exists(0, $file_data) === true)
		{
			return ($file_data[0]['error'] === UPLOAD_ERR_NO_FILE);
		}

		// Single file upload
		if (is_array($file_data['error']) === false)
		{
			return ($file_data['error'] === UPLOAD_ERR_NO_FILE);
		}

		// Multi-file upload
		return (count($file_data['error']) === 1 && $file_data['error'][0] === UPLOAD_ERR_NO_FILE);
	}

	/**
	 * Converts the multi-uploads in $_FILES array to a readable format.
	 *
	 * @param array<mixed,mixed|array<string|integer,mixed>> $files The field array in the file array to format.
	 * @return array<integer,array<string,mixed>>
	 */
	public static function transposeMultiFileData(array $files): array
	{
		// Return if formatted already
		if (is_array($files) && (empty($files) || isset($files[0])))
		{
			return $files;
		}

		// don't format single files
		if (is_array($files['error']) === false)
		{
			return (self::isFileDataEmpty($files)) ? [] : $files;
		}

		$file_count       = count($files['error']);
		$file_keys        = array_keys($files);
		$transposed_array = [];
		$new_file_counter = 0;

		for ($i = 0; $i < $file_count; $i++)
		{
			if ($files['error'][$i] === UPLOAD_ERR_NO_FILE)
			{
				continue;
			}

			$transposed_array[$new_file_counter] = [];

			foreach ($file_keys as $key)
			{
				$transposed_array[$new_file_counter][$key] = $files[$key][$i];
			}

			$new_file_counter++;
		}

		return $transposed_array;
	}
}