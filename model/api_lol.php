<?php header('Content-Type: charset=utf-8'); ?>
<?php
class ApiLol{
	
	private $URL = ['base'=>'https://{proxy}.api.pvp.net/api/lol/{region}/{url}',
					'summoner_by_name'=>'v{version}/summoner/by-name/{names}',
					'current_game'=>'https://br.api.pvp.net/observer-mode/rest/consumer/getSpectatorGameInfo/BR1/{player_id}',
					'summoner_tier'=>'v{version}/league/by-summoner/{player_id}/entry',
					'summoner_spell'=>'https://ddragon.leagueoflegends.com/cdn/{version_game}/img/spell/{spell_name}.png',
					'icon_champion'=>'https://ddragon.leagueoflegends.com/cdn/{version_game}/img/champion/{champion_name}.png',
					'global_icon_champ'=>'static-data/{region}/v{static-data}/champion/{id}'];
	private $API_VERSIONS = ['summoner'=>'1.4',
							 'league'=>'2.5',
							 'version_game'=>'6.24.1',
							 'static-data'=>'1.2'];
	private $REGIONS = ['brazil'=>'br',
						'eune'=>'eune',
						'euw'=>'euw',
						'korea'=>'kr',
						'na'=>'na'];
						
	private $SUMMONER_KEY_SPELL = ['1'  => 'SummonerBoost',    //Cleanse
								   '3'  => 'SummonerExhaust',  //Exhaust
								   '4'  => 'SummonerFlash',    //Flash
								   '6'  => 'SummonerHaste',    //Ghost
								   '7'  => 'SummonerHeal',     //Heal
								   '11' => 'SummonerSmite',    //Smite
								   '12' => 'SummonerTeleport', //Teleport
								   '13' => 'SummonerMana',     //Clarity
								   '14' => 'SummonerDot',      //Ignite
								   '21' => 'SummonerBarrier'];  //Barrier
								   
			
	private $API_KEY = '35232519-4d8a-4da3-ade6-ea6648b577dc';
	//private $nome_jogador = "É o Jovicone";
	//private $nome_jogador = "gratis 150ml";
	//private $nome_jogador = "Rakin Natalino";
	//private $nome_jogador = "Denieus";
	//private $nome_jogador = "im cheed";
	//private $nome_jogador = "Numerico";
	private $nome_jogador;
	
	function __construct(){
		$this->nome_jogador = @$_GET['nome']; //seta o nome via parametro get
	}
	
	function formatUrlGlobal($urlParam){
		$url = str_replace('{proxy}','global',$this->URL['base']);
		$url = str_replace('{region}/{url}',$urlParam, $url);
		$urlFinal = $url . "?api_key=".$this->API_KEY;
		return $urlFinal;
	}
	
	function formatUrlBrazil($urlParam){
		$url = str_replace('{proxy}',$this->REGIONS['brazil'],$this->URL['base']);
		$url = str_replace('{region}',$this->REGIONS['brazil'],$url);
		$url = str_replace('{url}',$urlParam,$url);
		$urlFinal = $url . "?api_key=".$this->API_KEY;
		//print_r($urlFinal);
		return $urlFinal;
	}
	
	function openUrl($url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_URL, $url);
		$data = curl_exec($curl);
		$status = curl_getinfo($curl);
		//print_r( $status);
		curl_close($curl);
		
		if($status['http_code']==200){
			return json_decode($data, true);
		}else{
			return '404';
		}
		
	}
	
	function getIdNamePlayer(){
		$summName = str_replace('{version}',$this->API_VERSIONS['summoner'],$this->URL['summoner_by_name']);
		$nickname = str_replace(' ','',$this->nome_jogador);
		$summName = str_replace('{names}',$nickname,$summName);
		
		$urlFinal = $this->formatUrlBrazil($summName);
		
		$data = $this->openUrl($urlFinal) ;
		
		return $data[strtolower($nickname)]['id'];
		
	}
	
	function getCurrentTier($player_id){
		$summTier = str_replace('{version}',$this->API_VERSIONS['league'],$this->URL['summoner_tier']);
		$summTier = str_replace('{player_id}',$player_id,$summTier);
		
		$urlFinal = $this->formatUrlBrazil($summTier); 
		$data = $this->openUrl($urlFinal);
		if($data !== '404'){
			
			echo $data[$player_id][0]['tier'];
			echo $data[$player_id][0]['entries'][0]['division'];
			echo "\n";
		}else echo "Unranked";
	
	}
	
	function getSummonerSpell($key){
		$url = str_replace('{version_game}',$this->API_VERSIONS['version_game'],$this->URL['summoner_spell']);
		$urlFinal = str_replace('{spell_name}',$this->SUMMONER_KEY_SPELL[$key], $url);
		return $urlFinal;
	}
	
	function getIconChampion($champId){
		$url = str_replace('{static-data}',$this->API_VERSIONS['static-data'], $this->URL['global_icon_champ']);
		$url = str_replace('{region}',$this->REGIONS['brazil'], $url);
		$url = str_replace('{id}',$champId, $url);
		
		$urlGetInfoChamp = $this->formatUrlGlobal($url);
		
		$dataChampion = $this->openUrl($urlGetInfoChamp);
		//print_r($dataChampion);
		//echo $dataChampion['key']; 
		
		$url = str_replace('{champion_name}',$dataChampion['key'], $this->URL['icon_champion']);
		$urlFinal = str_replace('{version_game}',$this->API_VERSIONS['version_game'], $url);
		
		return $urlFinal;
		
	}
	
	
	function current_game($player_id){
		$summCurrent = str_replace('{player_id}',$player_id,$this->URL['current_game']);
		$urlFinal = $summCurrent . "?api_key=".$this->API_KEY;
		
		
		$data = $this->openUrl($urlFinal);

		return $data;
		
	}
	
	function format_html_current_game($jogador){
		echo "<p>".$jogador['gameMode'] ."-". $jogador['gameType']."</p> \n";
		echo "<ul class='team-blue'>\n";
		echo "<h1>Team Blue</h1>";
		for($i=0; $i<5; $i++){
		
			echo "<li >";
			echo "<p>".$jogador['participants'][$i]['summonerName'] . "</p>";
			echo "<p>".$this->getCurrentTier( $jogador['participants'][$i]['summonerId'] ). "</p>";
			echo "<img width='45px' src='".$this->getIconChampion($jogador['participants'][$i]['championId']) . "'/> \n";
			echo "<img width='45px' src='".$this->getSummonerSpell($jogador['participants'][$i]['spell1Id']) ."'/> \n";
			echo "<img width='45px' src='".$this->getSummonerSpell($jogador['participants'][$i]['spell2Id']) ."'/> \n";
			echo "</li> \n";
		}
		echo "</ul>";
		echo "<ul class='team-red'>";
		echo "<h1>Team Red</h1>";
		for($j=5; $j<10; $j++){
				
			echo "<li>";
			echo "<p>".$jogador['participants'][$j]['summonerName'] . "</p>";
			echo "<p>".$this->getCurrentTier( $jogador['participants'][$j]['summonerId']) . "</p>";
			echo "<img width='45px' src='".$this->getIconChampion($jogador['participants'][$j]['championId']) . "'/> \n";
			echo "<img width='45px' src='".$this->getSummonerSpell($jogador['participants'][$j]['spell1Id']) ."'/> \n";
			echo "<img width='45px' src='".$this->getSummonerSpell($jogador['participants'][$j]['spell2Id']) ."'/> \n";
			echo "</li> \n";

		}
			
		
		echo "</ul>";
		
	}
	
	function Main(){
		//echo $this->getIdNamePlayer($this->nome_jogador);
		
		/* Individual Player*/
		$id = $this->getIdNamePlayer();
		//$this->getCurrentTier($id);
		/* ------------------------------ */
		
		
		/* Current Match */
		$dataFinal = $this->current_game($id);
		if($dataFinal !== '404'){
			$this->format_html_current_game($dataFinal);
		}else
			echo "<h2>Nenhum jogo no momento</h2>";
		/* --------------*/
		//$this->getIconChampion(121);
	}
	



}
?>