<?

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/rest/src/classes/Employee.php");

if(CModule::IncludeModule("iblock")):
	// Принимаем и очищаем данные сотрудника из запроса
	if($_SERVER['REQUEST_METHOD'] == 'POST') {

		$requestBody = json_decode(file_get_contents('php://input'));
		$headers = implode("\n", getallheaders());
		$date = new DateTime();
		$date = $date->format("y:m:d h:i:s");
		$str = $date . $headers . file_get_contents('php://input');
		$filename = __DIR__.'/log.txt';

		file_put_contents($filename, PHP_EOL . $str, FILE_APPEND); // Лог

		$employeeFields = new EmployeeFields($requestBody->sectionId, $requestBody->name);
		$employeeProps = new EmployeeProps(
			$employeeFields,
			array(
			'BIRTH_DAY' => $requestBody->birthDay,
			'POSITION' => $requestBody->position, 
			'DEPARTMENT' => "",
			'GUID_ZUP' => $requestBody->guidZup, 
			'DISMISS_DATE' => $requestBody->dismissDate
			)
		);
		$employee = new Employee($employeeFields, $employeeProps);
	} else {
		die('Нет доступа');
	}
	
	if(!empty($res = $employee->isExists())) {
		$employee->update($res, $USER->GetID());
	} else {
		$employee->create($USER->GetID());
	}
endif;


?>
