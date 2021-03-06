<?

class WeatherStation extends IPSModule
{
	// helper properties
	private $position = 0;

	public function Create()
	{
		//Never delete this line!
		parent::Create();

		//These lines are parsed on Symcon Startup or Instance creation
		//You cannot use variables here. Just static values.

		$this->RequireParent("{8062CF2B-600E-41D6-AD4B-1BA66C32D6ED}");

		$this->RegisterPropertyString('Wunderground_Station_ID', '');
		$this->RegisterPropertyString('Wunderground_Station_Password', '');
		$this->RegisterPropertyString('Weathercloud_ID', '');
		$this->RegisterPropertyString('Weathercloud_Key', '');
		$this->RegisterPropertyString("ApiKey", "");
		$this->RegisterPropertyString("ApplicationKey", "ac14c2f0d58541d0a4714e51d97785e95728ce6ade5743dc8bec238fcc2c715b"); // API_Development_Key
		$this->RegisterPropertyString("MAC", "");
		$this->RegisterPropertyString("Ambient_Passkey", "");
		$this->RegisterPropertyInteger('temp_unit', 1);
		$this->RegisterPropertyInteger('speed_unit', 1);
		$this->RegisterPropertyInteger('pressure_unit', 1);
		$this->RegisterPropertyInteger('rain_unit', 1);

		$this->RegisterPropertyInteger('UpdateInterval_Wunderground', 20);
		$this->RegisterTimer('WundergroundTimerUpdate', 0, 'WeatherStation_Update_Wunderground(' . $this->InstanceID . ');');

		$this->RegisterPropertyInteger('UpdateInterval_Weathercloud', 10);
		$this->RegisterTimer('WeathercloudTimerUpdate', 0, 'WeatherStation_Update_Weathercloud(' . $this->InstanceID . ');');

		$this->RegisterPropertyInteger('UpdateInterval_Weatherbug', 10);
		$this->RegisterTimer('WeatherbugTimerUpdate', 0, 'WeatherStation_Update_Weatherbug(' . $this->InstanceID . ');');

		$this->RegisterPropertyInteger('UpdateInterval_AmbientWeather', 10);
		$this->RegisterTimer('AmbientWeatherTimerUpdate', 0, 'WeatherStation_Update_AmbientWeatherCloud(' . $this->InstanceID . ');');
	}

	public function ApplyChanges()
	{
		//Never delete this line!
		parent::ApplyChanges();

		$temp_unit = $this->ReadPropertyInteger("temp_unit");
		$speed_unit = $this->ReadPropertyInteger("speed_unit");
		$pressure_unit = $this->ReadPropertyInteger("pressure_unit");

		if ($temp_unit == 1) {
			$this->RegisterVariableFloat("Indoor_Temp", $this->Translate("Indoor Temperature"), "~Temperature", $this->_getPosition());
			$this->RegisterVariableFloat("Outdoor_Temp", $this->Translate("Outdoor Temperature"), "~Temperature", $this->_getPosition());
			$this->RegisterVariableFloat("Windchill", $this->Translate("Windchill"), "~Temperature", $this->_getPosition());
			$this->RegisterVariableFloat("Dewpoint", $this->Translate("Dewpoint"), "~Temperature", $this->_getPosition());
		} else {
			$this->RegisterVariableFloat("Indoor_Temp", $this->Translate("Indoor Temperature"), "~Temperature.Fahrenheit", $this->_getPosition());
			$this->RegisterVariableFloat("Outdoor_Temp", $this->Translate("Outdoor Temperature"), "~Temperature.Fahrenheit", $this->_getPosition());
			$this->RegisterVariableFloat("Windchill", $this->Translate("Windchill"), "~Temperature.Fahrenheit", $this->_getPosition());
			$this->RegisterVariableFloat("Dewpoint", $this->Translate("Dewpoint"), "~Temperature.Fahrenheit", $this->_getPosition());
		}


		$this->RegisterVariableFloat("Indoor_Humidity", $this->Translate("Indoor Humidity"), "~Humidity.F", $this->_getPosition());
		$this->RegisterVariableFloat("Outdoor_Humidity", $this->Translate("Outdoor Humidity"), "~Humidity.F", $this->_getPosition());
		if ($speed_unit == 1) {
			$this->RegisterVariableFloat("Windspeed_km", $this->Translate("Windspeed"), "~WindSpeed.kmh", $this->_getPosition());
			$this->RegisterVariableFloat("Windspeed_ms", $this->Translate("Windspeed"), "~WindSpeed.ms", $this->_getPosition());
			$this->RegisterVariableFloat("Windgust", $this->Translate("Wind gust"), "~WindSpeed.ms", $this->_getPosition());
		} else {
			$this->RegisterVariableFloat("Windspeed_km", $this->Translate("Windspeed"), "~WindSpeed.kmh", $this->_getPosition());
			$this->RegisterVariableFloat("Windspeed_ms", $this->Translate("Windspeed"), "~WindSpeed.ms", $this->_getPosition());
			$this->RegisterVariableFloat("Windgust", $this->Translate("Windgust"), "~WindSpeed.ms", $this->_getPosition());
		}


		$this->RegisterVariableInteger("Wind_Direction", $this->Translate("Wind Direction"), "~WindDirection", $this->_getPosition());

		if ($pressure_unit == 1) {
			$this->RegisterVariableFloat("absbaromin", $this->Translate("Air Pressure absolut"), "~AirPressure.F", $this->_getPosition());
			$this->RegisterVariableFloat("baromin", $this->Translate("Air Pressure"), "~AirPressure.F", $this->_getPosition());
		} else {
			$this->RegisterVariableFloat("absbaromin", $this->Translate("Air Pressure absolut"), "~AirPressure.F", $this->_getPosition());
			$this->RegisterVariableFloat("baromin", $this->Translate("Air Pressure"), "~AirPressure.F", $this->_getPosition());
		}

		$this->RegisterVariableFloat("rainin", $this->Translate("Rain"), "~Rainfall", $this->_getPosition());
		$this->RegisterVariableFloat("dailyrainin", $this->Translate("Daily Rain"), "~Rainfall", $this->_getPosition());
		$this->RegisterVariableFloat("weeklyrainin", $this->Translate("Weekly Rain"), "~Rainfall", $this->_getPosition());
		$this->RegisterVariableFloat("monthlyrainin", $this->Translate("Monthly Rain"), "~Rainfall", $this->_getPosition());
		$this->RegisterVariableFloat("solarradiation", $this->Translate("Solar Radiation"), "", $this->_getPosition());
		$this->RegisterVariableInteger("UV", $this->Translate("UV"), "", $this->_getPosition());
		$this->RegisterVariableString("Date", $this->Translate("Date"), "", $this->_getPosition());
		$this->RegisterVariableString("Software_Type", $this->Translate("Software Type"), "", $this->_getPosition());
		$this->RegisterVariableString("Action", $this->Translate("Action"), "", $this->_getPosition());
		$this->RegisterVariableInteger("Realtime", $this->Translate("Realtime"), "", $this->_getPosition());
		$this->RegisterVariableInteger("Frequence", $this->Translate("Frequence"), "", $this->_getPosition());


		$this->ValidateConfiguration();

	}

	/**
	 * Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
	 * Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC wie folgt zur Verfügung gestellt:
	 */

	private function ValidateConfiguration()
	{
		$mac = $this->ReadPropertyString('MAC');
		if ($mac == "") {
			$this->SetStatus(201);
		} else {
			// set interval
			$this->SetUpdateIntervallWunderground();
			$this->SetUpdateIntervallWeathercloud();
			$this->SetUpdateIntervallWeatherbug();
			$this->SetUpdateIntervallAmbientWeather();
			$this->SetStatus(102);
		}
	}

	/**
	 * Update Interval Wunderground
	 * set / unset update interval
	 */
	protected function SetUpdateIntervallWunderground()
	{
		$interval = $this->ReadPropertyInteger('UpdateInterval_Wunderground') * 1000;
		$this->SetTimerInterval('WundergroundTimerUpdate', $interval);
	}

	/**
	 * Update Interval Weathercloud
	 * set / unset update interval
	 */
	protected function SetUpdateIntervallWeathercloud()
	{
		$interval = $this->ReadPropertyInteger('UpdateInterval_Weathercloud') * 1000 * 60;
		$this->SetTimerInterval('WeathercloudTimerUpdate', $interval);
	}

	/**
	 * Update Interval Weatherbug
	 * set / unset update interval
	 */
	protected function SetUpdateIntervallWeatherbug()
	{
		$interval = $this->ReadPropertyInteger('UpdateInterval_Weatherbug') * 1000 * 60;
		$this->SetTimerInterval('WeatherbugTimerUpdate', $interval);
	}

	/**
	 * Update Interval Ambient Weather
	 * set / unset update interval
	 */
	protected function SetUpdateIntervallAmbientWeather()
	{
		$interval = $this->ReadPropertyInteger('UpdateInterval_AmbientWeather') * 1000;
		$this->SetTimerInterval('AmbientWeatherTimerUpdate', $interval);
	}

	public function GetData()
	{

	}

	protected function CelsiusToFahrenheit(float $celsius)
	{
		return $celsius * 1.8 + 32;
	}

	protected function FahrenheitToCelsius(float $fahrenheit)
	{
		return ($fahrenheit - 32) / 1.8;
	}

	protected function MilesToKilometer(float $mph)
	{
		$kmh = $mph * 1.609344;
		return $kmh;
	}

	protected function MilesToKN(float $mph)
	{
		$kn = $mph * 0.86897624190065;
		return $kn;
	}

	protected function KilometerToKN(float $kmh)
	{
		$kn = $kmh / 1.852;
		return $kn;
	}

	protected function MPHToMS(float $mph)
	{
		$ms = $mph * 0.44704;
		return $ms;
	}

	protected function MSToMPH(float $ms)
	{
		$mph = $ms * 2.23694;
		return $mph;
	}

	protected function Pressure(float $pressure)
	{
		$pascal = $pressure / 0.02952998751;
		return $pascal;
	}
	
	protected function Rain(float $inch)
	{
		$mm = $inch * 25.4;
		return $mm;
	}

	protected function SendToIO(string $payload)
	{
		$result = $this->SendDataToParent(json_encode(Array("DataID" => "{C8792760-65CF-4C53-B5C7-A30FCC84FEFE}", "Buffer" => $payload))); // TX Server Socket
		return $result;
	}

	public function ReceiveData($JSONString)
	{

		$this->SendDebug("Weatherstation:", $JSONString, 0);
		$payload = json_decode($JSONString);
		if(isset($payload->Type))
		{
			$type = $payload->Type;
			if ($type == 0) {
				$this->SendDebug("Weatherstation:", json_encode($payload->Buffer), 0);
				$this->WriteData($payload->Buffer);
			}
		}
	}

	protected function WriteData($payloadraw)
	{
		$payload = substr($payloadraw, 4, strlen($payloadraw) - 4);
		$url = "http://192.168.1.1/1.php?" . $payload;
		$this->SendDebug("Weatherstation:", $url, 0);
		$query = parse_url($url, PHP_URL_QUERY);
		parse_str($query, $data);
		$temp_unit = $this->ReadPropertyInteger("temp_unit");
		$speed_unit = $this->ReadPropertyInteger("speed_unit");
		$pressure_unit = $this->ReadPropertyInteger("pressure_unit");
		$indoor_temperature = $data["indoortempf"];
		$this->SendDebug("Weatherstation:", "indoor temperature: " . $indoor_temperature, 0);
		$temperature = $data["tempf"];
		$this->SendDebug("Weatherstation:", "temperature: " . $temperature, 0);
		$dewpoint = $data["dewptf"];
		$this->SendDebug("Weatherstation:", "dewpoint: " . $dewpoint, 0);
		$windchill = $data["windchillf"];
		$this->SendDebug("Weatherstation:", "windchill: " . $windchill, 0);
		if ($temp_unit == 1) {
			$this->SetValue("Indoor_Temp", $this->FahrenheitToCelsius($indoor_temperature));
			$this->SetValue("Outdoor_Temp", $this->FahrenheitToCelsius($temperature));
			$this->SetValue("Windchill", $this->FahrenheitToCelsius($windchill));
			$this->SetValue("Dewpoint", $this->FahrenheitToCelsius($dewpoint));
		} else {
			$this->SetValue("Indoor_Temp", $indoor_temperature);
			$this->SetValue("Outdoor_Temp", $temperature);
			$this->SetValue("Windchill", $dewpoint);
			$this->SetValue("Dewpoint", $windchill);
		}
		$indoorhumidity = $data["indoorhumidity"];
		$this->SendDebug("Weatherstation:", "indoor humidity: " . $indoorhumidity, 0);
		$humidity = $data["humidity"];
		$this->SendDebug("Weatherstation:", "windchill: " . $humidity, 0);
		$this->SetValue("Indoor_Humidity", $indoorhumidity);
		$this->SetValue("Outdoor_Humidity", $humidity);
		$windspeed = $data["windspeedmph"];
		$this->SendDebug("Weatherstation:", "windspeed: " . $windspeed, 0);
		$windgust = $data["windgustmph"];
		$this->SendDebug("Weatherstation:", "windgust: " . $windgust, 0);
		if ($speed_unit == 1) {
			$this->SetValue("Windspeed_km", $this->MilesToKilometer($windspeed));
			$this->SetValue("Windspeed_ms", $this->MPHToMS($windspeed));
			$this->SetValue("Windgust", $this->MilesToKilometer($windgust));
		} else {
			$this->SetValue("Windspeed_km", $windspeed);
			$this->SetValue("Windspeed_ms", $this->MPHToMS($windspeed));
			$this->SetValue("Windgust", $windgust);
		}
		$winddir = $data["winddir"];
		$this->SendDebug("Weatherstation:", "wind direction: " . $winddir, 0);
		$this->SetValue("Wind_Direction", $winddir);
		$absbaromin = $data["absbaromin"];
		$this->SendDebug("Weatherstation:", "barometer min: " . $absbaromin, 0);
		$baromin = $data["baromin"];
		$this->SendDebug("Weatherstation:", "abs barometer min: " . $baromin, 0);

		if ($pressure_unit == 1) {
			$this->SetValue("absbaromin", $this->Pressure($absbaromin));
			$this->SetValue("baromin", $this->Pressure($baromin));
		} else {
			$this->SetValue("absbaromin", $absbaromin);
			$this->SetValue("baromin", $baromin);
		}
		$rainin = $data["rainin"];
		$this->SendDebug("Weatherstation:", "rain: " . $rainin, 0);
		$dailyrainin = $data["dailyrainin"];
		$this->SendDebug("Weatherstation:", "daily rain: " . $dailyrainin, 0);
		$weeklyrainin = $data["weeklyrainin"];
		$this->SendDebug("Weatherstation:", "weekly rain: " . $weeklyrainin, 0);
		$monthlyrainin = $data["monthlyrainin"];
		$this->SendDebug("Weatherstation:", "monthly rain: " . $monthlyrainin, 0);
		$solarradiation = $data["solarradiation"];
		$this->SendDebug("Weatherstation:", "solar radiation: " . $solarradiation, 0);
		$uv = $data["UV"];
		$this->SendDebug("Weatherstation:", "uv: " . $uv, 0);
		$dateutc = $data["dateutc"];
		$this->SendDebug("Weatherstation:", "date utc: " . $dateutc, 0);
		$softwaretype = $data["softwaretype"];
		$this->SendDebug("Weatherstation:", "software type: " . $softwaretype, 0);
		$action = $data["action"];
		$this->SendDebug("Weatherstation:", "action: " . $action, 0);
		$realtime = $data["realtime"];
		$this->SendDebug("Weatherstation:", "realtime: " . $realtime, 0);
		$rtfreq = $data["rtfreq"];
		$this->SendDebug("Weatherstation:", "rt freq: " . $rtfreq, 0);

		$this->SetValue("rainin", $this->Rain($rainin));
		$this->SetValue("dailyrainin", $this->Rain($dailyrainin));
		$this->SetValue("weeklyrainin", $this->Rain($weeklyrainin));
		$this->SetValue("monthlyrainin", $this->Rain($monthlyrainin));
		$this->SetValue("solarradiation", $solarradiation);
		$this->SetValue("UV", $uv);
		$this->SetValue("Date", $dateutc);
		$this->SetValue("Software_Type", $softwaretype);
		$this->SetValue("Action", $action);
		$this->SetValue("Realtime", $realtime);
		$this->SetValue("Frequence", $rtfreq);
	}

	public function Update_Wunderground()
	{
		$wunderground_url = 'https://weatherstation.wunderground.com/weatherstation/updateweatherstation.php';
		$wunderground_station_id = $this->ReadPropertyString('Wunderground_Station_ID');
		$wunderground_station_password = $this->ReadPropertyString('Wunderground_Station_Password');
		// get data for wunderground

		$param = $this->GetParametersWunderground();

		$url = $wunderground_url . '?ID=' . $wunderground_station_id . '&PASSWORD=' . $wunderground_station_password . '&action=updateraw' . $param;
		$this->SendDebug("Weatherstation:", 'http-get: url=' . $url, 0);
		$time_start = microtime(true);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$wstatus = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		$duration = floor((microtime(true) - $time_start) * 100) / 100;
		$this->SendDebug("Weatherstation:", ' => httpcode=' . $httpcode . ', duration=' . $duration . 's', 0);
		// $do_abort = false;
		if ($httpcode != 200) {
			$err = " => got http-code $httpcode from wunderground";
			$this->SendDebug("Weatherstation:", $err, 0);
			// $do_abort = true;
		}
		$wstatus = trim($wstatus, "\n");
		if ($wstatus != 'success') {
			$err = ' => got from wunderground: ' . $wstatus;
			$this->SendDebug("Weatherstation:", $err, 0);
			//$do_abort = true;
		}
		/*
		if ($do_abort) {
			$this->SetValue('Wunderground', false);
			return -1;
		}
		$this->SetValue('Wunderground', true);
		*/
	}

	protected function GetParametersWunderground()
	{
		$param = '&dateutc=' . rawurlencode(date('Y-m-d G:i:s', time()));
		$param .= '&indoortempf=' . rawurlencode($this->CelsiusToFahrenheit(GetValue($this->GetIDForIdent("Indoor_Temp"))));
		$param .= '&tempf=' . rawurlencode($this->CelsiusToFahrenheit(GetValue($this->GetIDForIdent("Outdoor_Temp"))));
		$param .= '&dewptf=' . rawurlencode($this->CelsiusToFahrenheit(GetValue($this->GetIDForIdent("Dewpoint"))));
		$param .= '&windchillf=' . rawurlencode($this->CelsiusToFahrenheit(GetValue($this->GetIDForIdent("Windchill"))));
		$param .= '&indoorhumidity=' . rawurlencode($this->CelsiusToFahrenheit(GetValue($this->GetIDForIdent("Indoor_Humidity"))));
		$param .= '&humidity=' . rawurlencode($this->CelsiusToFahrenheit(GetValue($this->GetIDForIdent("Outdoor_Humidity"))));
		$param .= '&windspeedmph=' . rawurlencode($this->MSToMPH(GetValue($this->GetIDForIdent("Windspeed_ms"))));
		$param .= '&windgustmph=' . rawurlencode($this->MSToMPH(GetValue($this->GetIDForIdent("Windgust"))));
		$param .= '&winddir=' . rawurlencode(GetValue($this->GetIDForIdent("Wind_Direction")));
		$param .= '&absbaromin=' . rawurlencode(GetValue($this->GetIDForIdent("absbaromin")));
		$param .= '&baromin=' . rawurlencode(GetValue($this->GetIDForIdent("baromin")));
		$param .= '&rainin=' . rawurlencode(GetValue($this->GetIDForIdent("rainin")));
		$param .= '&dailyrainin=' . rawurlencode(GetValue($this->GetIDForIdent("dailyrainin")));
		$param .= '&weeklyrainin=' . rawurlencode(GetValue($this->GetIDForIdent("weeklyrainin")));
		$param .= '&monthlyrainin=' . rawurlencode(GetValue($this->GetIDForIdent("monthlyrainin")));
		$param .= '&solarradiation=' . rawurlencode(GetValue($this->GetIDForIdent("solarradiation")));
		$param .= '&UV=' . rawurlencode(GetValue($this->GetIDForIdent("UV")));
		$param .= '&softwaretype=EasyWeatherV1.2.1';
		$param .= '&realtime=1';
		$param .= '&rtfreq=5';
		return $param;
	}

	public function Update_Weathercloud()
	{
		$weathercloud_url = 'http://api.weathercloud.net/v01/set';

		$weathercloud_station_id = $this->ReadPropertyString('Weathercloud_ID');
		$weathercloud_station_password = $this->ReadPropertyString('Weathercloud_Key');

		$url = $weathercloud_url . '?wid=' . $weathercloud_station_id . '&key=' . $weathercloud_station_password;

		$param = $this->GetParametersWeathercloud();
		$url = $url . $param;
		$this->SendDebug("Weatherstation:", $url, 0);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($httpcode != 200) {
			$err = " => got http-code $httpcode from weathercloud";
			$this->SendDebug("Weatherstation:", $err, 0);
		}
	}

	protected function GetParametersWeathercloud()
	{
		// Gesendet wird noch von Station  "rainrate" und "heat" ??

		$param = '&date=' . date('Ymd', time() - date('Z'));
		$param .= '&time=' . date('Hi', time() - date('Z'));
		$param .= '&tempin=' . intval(GetValue($this->GetIDForIdent("Indoor_Temp")) * 10);
		$param .= '&temp=' . intval(GetValue($this->GetIDForIdent("Outdoor_Temp")) * 10);
		$param .= '&dew=' . intval(GetValue($this->GetIDForIdent("Dewpoint")) * 10);
		$param .= '&chill=' . intval(GetValue($this->GetIDForIdent("Windchill")) * 10);
		$param .= '&humin=' . intval(GetValue($this->GetIDForIdent("Indoor_Humidity")));
		$param .= '&hum=' . intval(GetValue($this->GetIDForIdent("Outdoor_Humidity")));
		$param .= '&wspd=' . intval($this->KilometerToKN(GetValue($this->GetIDForIdent("Windspeed_ms"))) * 10);
		$param .= '&wspdhi=' . intval($this->KilometerToKN(GetValue($this->GetIDForIdent("Windgust"))) * 10);
		$param .= '&wspdavg=' . intval($this->KilometerToKN(GetValue($this->GetIDForIdent("Windgust"))) * 10);
		$param .= '&wdir=' . intval(GetValue($this->GetIDForIdent("Wind_Direction")));
		$param .= '&bar=' . intval(GetValue($this->GetIDForIdent("baromin")) * 10);
		$param .= '&rain=' . intval(GetValue($this->GetIDForIdent("rainin")));
		$param .= '&solarrad=' . intval(GetValue($this->GetIDForIdent("solarradiation")) * 10);
		$param .= '&uvi=' . intval(GetValue($this->GetIDForIdent("UV")));
		$param .= '&type=EasyWeather';
		$param .= '&ver=1.2.1';

		return $param;
	}

	public function Update_AmbientWeatherCloud()
	{
		$ambient_weather_cloud_url = 'https://api.ambientweather.net:80/endpoint?';
		$passkey = $this->ReadPropertyString("Ambient_Passkey");

		$param = $this->GetParametersAmbientWeatherCloud();
		$url = $ambient_weather_cloud_url . '?PASSKEY=' . $passkey .  '&stationtype=WS-1600-IP' . $param;
		$this->SendDebug("Weatherstation:", 'http-get: url=' . $url, 0);
		$time_start = microtime(true);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$wstatus = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		$duration = floor((microtime(true) - $time_start) * 100) / 100;
		$this->SendDebug("Weatherstation:", ' => httpcode=' . $httpcode . ', duration=' . $duration . 's', 0);
		if ($httpcode != 200) {
			$err = " => got http-code $httpcode from ambient weather";
			$this->SendDebug("Weatherstation:", $err, 0);
		}
		$wstatus = trim($wstatus, "\n");
		if ($wstatus != 'success') {
			$err = ' => got from ambient weather: ' . $wstatus;
			$this->SendDebug("Weatherstation:", $err, 0);
		}
	}

	protected function GetParametersAmbientWeatherCloud()
	{
		$param = '&stationtype=WS-1600-IP';
		$param .= '&dateutc=' . rawurlencode(date('Y-m-d+G:i:s', time()));
		$param .= '&winddir=' . rawurlencode(GetValue($this->GetIDForIdent("Wind_Direction")));
		$param .= '&windspeedmph=' . rawurlencode($this->FormatFloat($this->MSToMPH(GetValue($this->GetIDForIdent("Windspeed_ms")))));
		$param .= '&windgustmph=' . rawurlencode($this->FormatFloat($this->MSToMPH(GetValue($this->GetIDForIdent("Windgust")))));
		// &maxdailygust=4.47
		$param .= '&tempf=' . rawurlencode($this->CelsiusToFahrenheit(GetValue($this->GetIDForIdent("Outdoor_Temp"))));
		$param .= '&humidity=' . rawurlencode(GetValue($this->GetIDForIdent("Outdoor_Humidity")));
		// &hourlyrainin=0.00
		$param .= '&dailyrainin=' . rawurlencode($this->FormatFloat(GetValue($this->GetIDForIdent("dailyrainin"))));
		$param .= '&weeklyrainin=' . rawurlencode($this->FormatFloat(GetValue($this->GetIDForIdent("weeklyrainin"))));
		$param .= '&monthlyrainin=' . rawurlencode($this->FormatFloat(GetValue($this->GetIDForIdent("monthlyrainin"))));
		// &yearlyrainin=0.00
		// &totalrainin=0.00
		$param .= '&tempinf=' . rawurlencode($this->CelsiusToFahrenheit(GetValue($this->GetIDForIdent("Indoor_Temp"))));
		$param .= '&humidityin=' . rawurlencode(intval(GetValue($this->GetIDForIdent("Indoor_Humidity"))));
		$param .= '&baromrelin=' . rawurlencode($this->FormatFloat(GetValue($this->GetIDForIdent("baromin"))));
		$param .= '&baromabsin=' . rawurlencode($this->FormatFloat(GetValue($this->GetIDForIdent("absbaromin"))));
		$param .= '&uv=' . rawurlencode(GetValue($this->GetIDForIdent("UV")));
		$param .= '&solarradiation=' . rawurlencode(GetValue($this->GetIDForIdent("solarradiation")));
		/*
		$param .= '&dewptf=' . rawurlencode($this->CelsiusToFahrenheit(GetValue($this->GetIDForIdent("Dewpoint"))));
		$param .= '&windchillf=' . rawurlencode($this->CelsiusToFahrenheit(GetValue($this->GetIDForIdent("Windchill"))));
		$param .= '&rainin=' . rawurlencode($this->CelsiusToFahrenheit(GetValue($this->GetIDForIdent("rainin"))));
		*/
		return $param;
	}

	public function Update_Weatherbug()
	{
	}

	public function AlexaData()
	{
		$data = [];
		$data["Indoor_Temp"] = GetValue($this->GetIDForIdent("Indoor_Temp"));
		$data["Outdoor_Temp"] = GetValue($this->GetIDForIdent("Outdoor_Temp"));
		$data["Dewpoint"] = GetValue($this->GetIDForIdent("Dewpoint"));
		$data["Windchill"] = GetValue($this->GetIDForIdent("Windchill"));
		$data["Indoor_Humidity"] = GetValue($this->GetIDForIdent("Indoor_Humidity"));
		$data["Outdoor_Humidity"] = GetValue($this->GetIDForIdent("Outdoor_Humidity"));
		$data["Windspeed_km"] = GetValue($this->GetIDForIdent("Windspeed_km"));
		$data["Windgust"] = GetValue($this->GetIDForIdent("Windgust"));
		$data["Wind_Direction"] = GetValue($this->GetIDForIdent("Wind_Direction"));
		$data["absbaromin"] = GetValue($this->GetIDForIdent("absbaromin"));
		$data["baromin"] = GetValue($this->GetIDForIdent("baromin"));
		$data["rainin"] = GetValue($this->GetIDForIdent("rainin"));
		$data["dailyrainin"] = GetValue($this->GetIDForIdent("dailyrainin"));
		$data["weeklyrainin"] = GetValue($this->GetIDForIdent("weeklyrainin"));
		$data["monthlyrainin"] = GetValue($this->GetIDForIdent("monthlyrainin"));
		$data["solarradiation"] = GetValue($this->GetIDForIdent("solarradiation"));
		$data["UV"] = GetValue($this->GetIDForIdent("UV"));

		return $data;
	}

	/**
	 * gets current IP-Symcon version
	 * @return float|int
	 */
	protected function GetIPSVersion()
	{
		$ipsversion = floatval(IPS_GetKernelVersion());
		if ($ipsversion < 4.1) // 4.0
		{
			$ipsversion = 0;
		} elseif ($ipsversion >= 4.1 && $ipsversion < 4.2) // 4.1
		{
			$ipsversion = 1;
		} elseif ($ipsversion >= 4.2 && $ipsversion < 4.3) // 4.2
		{
			$ipsversion = 2;
		} elseif ($ipsversion >= 4.3 && $ipsversion < 4.4) // 4.3
		{
			$ipsversion = 3;
		} elseif ($ipsversion >= 4.4 && $ipsversion < 5) // 4.4
		{
			$ipsversion = 4;
		} else   // 5
		{
			$ipsversion = 5;
		}

		return $ipsversion;
	}

	//Profile
	protected function RegisterProfile($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize, $Digits, $Vartype)
	{

		if (!IPS_VariableProfileExists($Name)) {
			IPS_CreateVariableProfile($Name, $Vartype); // 0 boolean, 1 int, 2 float, 3 string,
		} else {
			$profile = IPS_GetVariableProfile($Name);
			if ($profile['ProfileType'] != $Vartype)
				$this->SendDebug("Weatherstation:", "Variable profile type does not match for profile " . $Name, 0);
		}

		IPS_SetVariableProfileIcon($Name, $Icon);
		IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
		IPS_SetVariableProfileDigits($Name, $Digits); //  Nachkommastellen
		IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize); // string $ProfilName, float $Minimalwert, float $Maximalwert, float $Schrittweite
	}

	protected function RegisterProfileAssociation($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $Stepsize, $Digits, $Vartype, $Associations)
	{
		if (sizeof($Associations) === 0) {
			$MinValue = 0;
			$MaxValue = 0;
		}

		$this->RegisterProfile($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $Stepsize, $Digits, $Vartype);

		//boolean IPS_SetVariableProfileAssociation ( string $ProfilName, float $Wert, string $Name, string $Icon, integer $Farbe )
		foreach ($Associations as $Association) {
			IPS_SetVariableProfileAssociation($Name, $Association[0], $Association[1], $Association[2], $Association[3]);
		}

	}

	protected function FormatFloat($value)
	{
		$formatted_value = str_replace(",", ".", $value);
		return $formatted_value;
	}

	/**
	 * return incremented position
	 * @return int
	 */
	private function _getPosition()
	{
		$this->position++;
		return $this->position;
	}

	/***********************************************************
	 * Configuration Form
	 ***********************************************************/

	/**
	 * build configuration form
	 * @return string
	 */
	public function GetConfigurationForm()
	{
		// return current form
		return json_encode([
			'elements' => $this->FormHead(),
			'actions' => $this->FormActions(),
			'status' => $this->FormStatus()
		]);
	}

	/**
	 * return form configurations on configuration step
	 * @return array
	 */
	protected function FormHead()
	{
		$form = [
			[
				'type' => 'Label',
				'label' => 'Data for Ambient Weather'
			],
			[
				'type' => 'Label',
				'label' => 'MAC adress'
			],
			[
				'name' => 'MAC',
				'type' => 'ValidationTextBox',
				'caption' => 'MAC'
			],
			[
				'type' => 'Label',
				'label' => 'Ambient Weather Passkey'
			],
			[
				'name' => 'Ambient_Passkey',
				'type' => 'ValidationTextBox',
				'caption' => 'Passkey'
			],
			[
				'type' => 'Label',
				'label' => 'Wunderground Station ID'
			],
			[
				'name' => 'Wunderground_Station_ID',
				'type' => 'ValidationTextBox',
				'caption' => 'Station ID'
			],
			[
				'type' => 'Label',
				'label' => 'Wunderground Station Password'
			],
			[
				'name' => 'Wunderground_Station_Password',
				'type' => 'ValidationTextBox',
				'caption' => 'Station Password'
			],
			[
				'type' => 'Label',
				'label' => 'Weathercloud ID'
			],
			[
				'name' => 'Weathercloud_ID',
				'type' => 'ValidationTextBox',
				'caption' => 'Weathercloud ID'
			],
			[
				'type' => 'Label',
				'label' => 'Weathercloud Key'
			],
			[
				'name' => 'Weathercloud_Key',
				'type' => 'ValidationTextBox',
				'caption' => 'Weathercloud Key'
			],
			[
				'type' => 'Label',
				'label' => 'Update Interval Wunderground (seconds)'
			],
			[
				'name' => 'UpdateInterval_Wunderground',
				'type' => 'IntervalBox',
				'caption' => 'Seconds'
			],
			[
				'type' => 'Label',
				'label' => 'Update Interval Weathercloud (minutes)'
			],
			[
				'name' => 'UpdateInterval_Weathercloud',
				'type' => 'IntervalBox',
				'caption' => 'Minutes'
			],
			[
				'type' => 'Label',
				'label' => 'Update Interval Weatherbug (minutes)'
			],
			[
				'name' => 'UpdateInterval_Weatherbug',
				'type' => 'IntervalBox',
				'caption' => 'Minutes'
			],
			[
				'type' => 'Label',
				'label' => 'Update Interval Ambient Weather (seconds)'
			],
			[
				'name' => 'UpdateInterval_AmbientWeather',
				'type' => 'IntervalBox',
				'caption' => 'Seconds'
			],
			[
				'type' => 'Label',
				'label' => 'Select units:'
			],
			[
				'name' => 'temp_unit',
				'type' => 'Select',
				'caption' => 'Temperature',
				'options' => [
					[
						'label' => 'Celius °C',
						'value' => 1
					],
					[
						'label' => 'Fahrenheit F',
						'value' => 2
					]
				]
			],
			[
				'name' => 'speed_unit',
				'type' => 'Select',
				'caption' => 'Wind Speed',
				'options' => [
					[
						'label' => 'kmh',
						'value' => 1
					],
					[
						'label' => 'mph',
						'value' => 2
					]
				]
			],
			[
				'name' => 'pressure_unit',
				'type' => 'Select',
				'caption' => 'Air Pressure',
				'options' => [
					[
						'label' => 'pascal',
						'value' => 1
					],
					[
						'label' => 'bar',
						'value' => 2
					]
				]
			],
			[
				'name' => 'rain_unit',
				'type' => 'Select',
				'caption' => 'Rain',
				'options' => [
					[
						'label' => 'mm',
						'value' => 1
					],
					[
						'label' => 'inch',
						'value' => 2
					]
				]
			]
		];
		return $form;
	}

	/**
	 * return form actions
	 * @return array
	 */
	protected function FormActions()
	{
		$form = [

		];

		return $form;
	}

	/*
	 * [
				'type' => 'Label',
				'label' => 'Update'
			],
			[
				'type' => 'Button',
				'label' => 'labelname',
				'onClick' => 'WeatherStation_GetData($id);'
			]
	 */

	/**
	 * return from status
	 * @return array
	 */
	protected function FormStatus()
	{
		$form = [
			[
				'code' => 101,
				'icon' => 'inactive',
				'caption' => 'Creating instance.'
			],
			[
				'code' => 102,
				'icon' => 'active',
				'caption' => 'Device created.'
			],
			[
				'code' => 104,
				'icon' => 'inactive',
				'caption' => 'interface closed.'
			],
			[
				'code' => 201,
				'icon' => 'error',
				'caption' => 'MAC must not be empty'
			]
		];

		return $form;
	}

	//Add this Polyfill for IP-Symcon 4.4 and older
	protected function SetValue($Ident, $Value)
	{

		if (IPS_GetKernelVersion() >= 5) {
			parent::SetValue($Ident, $Value);
		} else {
			SetValue($this->GetIDForIdent($Ident), $Value);
		}
	}

	// Readme
	/*
	 * Im Anschluss ist ein Account für das Dashboard anzulegen
[Dashboard Ambient Weather](https://dashboard.ambientweather.net/ "Ambient Weather Dashboard")

	Bei IFTTT die Station koppeln
[IFTTT Ambient Weather](https://ifttt.com/ambient_weather "IFTTT Ambient Weather")

Bei Wunderground die Wetterstation anlegen
[Wunderground PWS](https://ifttt.com/ambient_weather "Wunderground PWS")
	 */
}

?>
