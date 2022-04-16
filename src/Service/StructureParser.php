<?php

namespace Migrator\Service;

/**
 * Class StructureParser.
 * This class parses the fields inside the migration.
 * @package Migrator\Service
 */
Class StructureParser {

    /**
     * @var array Field matches that are not structured
     */
    public $matches;

    /**
     * @var array Structured field list
     */
    public $structure = array();

    /**
     * StructureParser constructor.
     * @param array $matches
     */
    public function __construct($matches)
    {
        $this->matches = $matches;
    }

    /**
     * Get the structured field list of all matched fields.
     *
     * @return array
     */
    public function getStructure()
	{
		foreach($this->matches[0] as $match) {
			$match = trim(str_replace(';', '', $match));

			$match = substr($match, strpos($match, '->') + 2);
			$type = $this->getType($match);

			$this->structure[] = [
                'name'     => $this->getName($match, $type),
                'type'     => $type,
                'nullable' => $this->checkNullable($match),
                'unique'   => $this->checkUnique($match),
                'default'  => $this->checkDefault($match)
            ];
		}

		return array_filter($this->structure, function($value) {
		    return !is_null($value['type']);
		});
	}

    /**
     * Check if the field has the unique attribute.
     *
     * @param string $match Field
     * @return false|int
     */
    private function checkUnique($match)
	{
		return strpos($match, '->unique()');
	}

    /**
     * Check if the field has a default value.
     *
     * @param string $match Field
     * @return false|string
     */
    private function checkDefault($match)
	{
		if (!strpos($match, '->default(')) {
            return false;
        }

		preg_match('/->default\((.*?)\).*/', $match, $newMatch);

        return str_replace(['"', '\''], '', $newMatch[1] ?? '');
	}

    /**
     * Check if the field has the nullable attribute.
     *
     * @param string $match
     * @return false|int
     */
    private function checkNullable($match)
	{
		return strpos($match, '->nullable()');
	}

    /**
     * Get the name of field.
     *
     * @param string $match
     * @param string $type
     * @return false|string
     */
    private function getName($match, $type)
	{
	    $matches = [
	        'id' => 'id',
            'nullableTimestamps' => 'created_at, updated_at',
            'timestampsTz' => 'created_at, updated_at',
            'timestamps' => 'created_at, updated_at',
            'rememberToken' => 'remember_token',
        ];

	    if (in_array($type, array_keys($matches))){
	        return $matches[$type];
        }

	    if (in_array($type, ['enum', 'set'])){
	        return substr($match, stripos($match, "'") + 1, (stripos($match, ",") - stripos($match, "'") - 2));
        }

	    if ($type === 'foreignIdFor'){
	        return substr($match, stripos($match, "(") + 1, (stripos($match, ")") - stripos($match, "(") - 1));
        }

		return substr($match, stripos($match, "'") + 1, stripos(substr($match, stripos($match, "'") + 1), "'"));
	}

    /**
     * Get the type of field.
     *
     * @param string $match
     * @return mixed|string
     */
    private function getType($match)
	{
        preg_match("/^(\w+)\(.*\).*/si", $match, $newMatches);

        return $newMatches[1] ?? '';
	}
}
