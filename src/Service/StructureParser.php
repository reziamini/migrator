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
			
			$output = $this->checkNullable($match);
				
			$match = $output[0]['match'];
			$nullable = $output[0]['nullable'];
			
			$output = $this->checkUnique($match);
				
			$match = $output[0]['match'];
			$unique = $output[0]['unique'];
			
			$output = $this->checkDefault($match);
				
			$match = $output[0]['match'];
			$dafaultValue = $output[0]['defaultValue'];
			
			$this->structure[] = [
									'name'     => $this->getName($match, $this->getType($match)), 
									'type'     => $this->getType($match),
									'nullable' => $nullable,
									'unique'   => $unique,
									'dafault'  => $dafaultValue
								 ];
			
		}
		
		return array_filter($this->structure, fn($value) => !is_null($value['type']));
	}
	
	private function checkUnique($match)
	{
		if (strpos($match, '->unique()')) {
			$match = str_replace('->unique()', '', $match);
			return array(['unique' => true, 'match' => $match]);
		}
		
		return array(['unique' => false, 'match' => $match]);
	}
	
	private function checkDefault($match)
	{
		
		if (strpos($match, '->default(')) {
			
			$string = substr($match, strpos($match, '->default(') + 2);
			
			$match = substr($match, 0, strpos($match, '->default('));
			
			$dafaultValue = substr($string, stripos($string, "(") + 1, (strrpos($string, ")") - stripos($string, "(") - 1));
			
			return array(['defaultValue' => $dafaultValue, 'match' => $match]);
		}
		
		return array(['defaultValue' => false, 'match' => $match]);
	}
	
	private function checkNullable($match)
	{
		if (strpos($match, '->nullable()')) {
			$match = str_replace('->nullable()', '', $match);
			return array(['nullable' => true, 'match' => $match]);
		}
		
		return array(['nullable' => false, 'match' => $match]);
	}
	
	private function getName($match, $type)
	{
		if ($type === 'enum' || $type === 'set') {
			return substr($match, stripos($match, "'") + 1, (stripos($match, ",") - stripos($match, "'") - 2));
		}
		
		if ($type === 'id' || $type === 'nullableTimestamps' || $type === 'rememberToken' || $type === 'timestampsTz' || $type === 'timestamps') {
			return '';
		}
		
		if ($type === 'foreignIdFor') {
			return substr($match, stripos($match, "(") + 1, (stripos($match, ")") - stripos($match, "(") - 1));
		}
		
		return substr($match, stripos($match, "'") + 1, stripos(substr($match, stripos($match, "'") + 1), "'"));
	}
	
	private function getType($match)
	{
		if (strpos($match, 'bigIncrements(') === 0)
			return 'bigIncrements';
		
		if (strpos($match, 'bigInteger(') === 0)
			return 'bigInteger';
		
		if (strpos($match, 'binary(') === 0)
			return 'binary';
		
		if (strpos($match, 'boolean(') === 0)
			return 'boolean';
		
		if (strpos($match, 'char(') === 0)
			return 'char';
		
		if (strpos($match, 'dateTimeTz(') === 0)
			return 'dateTimeTz';
		
		if (strpos($match, 'dateTime(') === 0)
			return 'dateTime';
		
		if (strpos($match, 'date(') === 0)
			return 'date';
		
		if (strpos($match, 'decimal(') === 0)
			return 'decimal';
		
		if (strpos($match, 'double(') === 0)
			return 'double';
		
		if (strpos($match, 'enum(') === 0)
			return 'enum';
		
		if (strpos($match, 'float(') === 0)
			return 'float';
		
		if (strpos($match, 'foreignId(') === 0)
			return 'foreignId';
		
		if (strpos($match, 'foreignIdFor(') === 0)
			return 'foreignIdFor';
		
		if (strpos($match, 'foreignUuid(') === 0)
			return 'foreignUuid';
		
		if (strpos($match, 'geometryCollection(') === 0)
			return 'geometryCollection';
		
		if (strpos($match, 'geometry(') === 0)
			return 'geometry';
		
		if (strpos($match, 'id') === 0)
			return 'id';
		
		if (strpos($match, 'increments(') === 0)
			return 'increments';
		
		if (strpos($match, 'integer(') === 0)
			return 'integer';
		
		if (strpos($match, 'ipAddress(') === 0)
			return 'ipAddress';
		
		if (strpos($match, 'json(') === 0)
			return 'json';
		
		if (strpos($match, 'jsonb(') === 0)
			return 'jsonb';
		
		if (strpos($match, 'lineString(') === 0)
			return 'lineString';
		
		if (strpos($match, 'longText(') === 0)
			return 'longText';
		
		if (strpos($match, 'macAddress(') === 0)
			return 'macAddress';
		
		if (strpos($match, 'mediumIncrements(') === 0)
			return 'mediumIncrements';
		
		if (strpos($match, 'mediumInteger(') === 0)
			return 'mediumInteger';
		
		if (strpos($match, 'mediumText(') === 0)
			return 'mediumText';
		
		if (strpos($match, 'morphs(') === 0)
			return 'morphs';
		
		if (strpos($match, 'multiLineString(') === 0)
			return 'multiLineString';
		
		if (strpos($match, 'multiPoint(') === 0)
			return 'multiPoint';
		
		if (strpos($match, 'multiPolygon(') === 0)
			return 'multiPolygon';
		
		if (strpos($match, 'nullableTimestamps(') === 0)
			return 'nullableTimestamps';
		
		if (strpos($match, 'nullableMorphs(') === 0)
			return 'nullableMorphs';
		
		if (strpos($match, 'nullableUuidMorphs(') === 0)
			return 'nullableUuidMorphs';
		
		if (strpos($match, 'point(') === 0)
			return 'point';
		
		if (strpos($match, 'polygon(') === 0)
			return 'polygon';
		
		if (strpos($match, 'rememberToken(') === 0)
			return 'rememberToken';
		
		if (strpos($match, 'set(') === 0)
			return 'set';
		
		if (strpos($match, 'smallIncrements(') === 0)
			return 'smallIncrements';
		
		if (strpos($match, 'smallInteger(') === 0)
			return 'smallInteger';
		
		if (strpos($match, 'softDeletesTz(') === 0)
			return 'softDeletesTz';
		
		if (strpos($match, 'softDeletes(') === 0)
			return 'softDeletes';
		
		if (strpos($match, 'string(') === 0)
			return 'string';
		
		if (strpos($match, 'text(') === 0)
			return 'text';
		
		if (strpos($match, 'timeTz(') === 0)
			return 'timeTz';
		
		if (strpos($match, 'time(') === 0)
			return 'time';
		
		if (strpos($match, 'timestampTz(') === 0)
			return 'timestampTz';
		
		if (strpos($match, 'timestamp(') === 0)
			return 'timestamp';
		
		if (strpos($match, 'timestampsTz(') === 0)
			return 'timestampsTz';
		
		if (strpos($match, 'timestamps(') === 0)
			return 'timestamps';
		
		if (strpos($match, 'tinyIncrements(') === 0)
			return 'tinyIncrements';
		
		if (strpos($match, 'tinyInteger(') === 0)
			return 'tinyInteger';
		
		if (strpos($match, 'tinyText(') === 0)
			return 'tinyText';
		
		if (strpos($match, 'unsignedBigInteger(') === 0)
			return 'unsignedBigInteger';
		
		if (strpos($match, 'unsignedDecimal(') === 0)
			return 'unsignedDecimal';
		
		if (strpos($match, 'unsignedInteger(') === 0)
			return 'unsignedInteger';
		
		if (strpos($match, 'unsignedMediumInteger(') === 0)
			return 'unsignedMediumInteger';
		
		if (strpos($match, 'unsignedSmallInteger(') === 0)
			return 'unsignedSmallInteger';
		
		if (strpos($match, 'unsignedTinyInteger(') === 0)
			return 'unsignedTinyInteger';
		
		if (strpos($match, 'uuidMorphs(') === 0)
			return 'uuidMorphs';
		
		if (strpos($match, 'uuid(') === 0)
			return 'uuid';
		
		if (strpos($match, 'year(') === 0)
			return 'year';
		
	}
}