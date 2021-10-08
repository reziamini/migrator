<?php

namespace Migrator\Service;

Class StructureParser {

	public $matches;
	public $structure = array();

	public function __construct($matches)
    {
        $this->matches = $matches;
    }

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

		return array_filter($this->structure, fn($value) => !is_null($value['type']));
	}

	private function checkUnique($match)
	{
		return strpos($match, '->unique()');
	}

	private function checkDefault($match)
	{
		if (!strpos($match, '->default(')) {
            return false;
        }

		preg_match('/->default\((.*?)\).*/', $match, $newMatch);

        return str_replace(['"', '\''], '', $newMatch[1]);
	}

	private function checkNullable($match)
	{
		return strpos($match, '->nullable()');
	}

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

	private function getType($match)
	{
        preg_match("/^(\w+)\(.*\).*/si", $match, $newMatches);

        return $newMatches[1];
	}
}
