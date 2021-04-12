<!DOCTYPE html>
<html>
	<head>
		<title>Ant Colony Optimization</title>
		<style>
			table{
				text-align: center;
			}
			.outline{
				border: 1px solid black;
				border-collapse: collapse;
			}
			.bg1{
				background: #ffd3b4;
			}
			.bg2{
				background: #ffaaa7;
			}
			.la{
				text-align: left;
			}
			.pl{
				padding-left: 10px;
			}
			.pr{
				padding-right: 10px;
			}
		</style>
	</head>
	<body>
		<?php
			echo "Kasus:<br>";
			echo "Pencarian rute terpendek jasa pengantaran pulang siswa menggunakan algoritma <i>Ant Colony Optimization</i> (ACO).<br><br>";
			function rand_float($start_number = 0, $end_number = 1, $mul = 1000000){
				if ($start_number > $end_number) return false;
				return mt_rand($start_number * $mul,$end_number * $mul)/$mul;
			}
			function cariJarak($c1, $c2){
				$hasil = ((($c1[0]-$c2[0])**2 + ($c1[1]-$c2[1])**2)**0.5) * 111.319;
				return $hasil;
			}
			function cariJarakTerpendek($jejak, $jarak){
				$hasil = 0;
				for ($i=0; $i < count($jejak)-1; $i++) { 
					$hasil += $jarak[$jejak[$i]][$jejak[$i+1]];
				}
				return $hasil;
			}
			function cariJejakTerbaik($semut, $jarak){
				$jarakterpendek = cariJarakTerpendek($semut[0], $jarak);
				$idxjarakterpendek = 0;
				for ($i=1; $i < count($semut); $i++) { 
					$totaljarak = cariJarakTerpendek($semut[$i], $jarak);
					if ($totaljarak < $jarakterpendek) {
						$jarakterpendek = $totaljarak;
						$idxjarakterpendek = $i;
					}
				}
				return $semut[$idxjarakterpendek];
			}
			function updateSemut($semut, $feromon, $jarak, $alpha, $beta){
				$jumlahtitik = count($feromon);
				for ($i=0; $i < count($semut); $i++) { 
					$titikawal = 0;
					$jejak = [];
					$titikterpakai = [];
					for ($j=0; $j < $jumlahtitik; $j++) { 
						array_push($jejak, 0);
						array_push($titikterpakai, 0);
					}
					$jejak[0] = $titikawal;
					$titikterpakai[$titikawal] = 1;
					for ($j=0; $j < $jumlahtitik-1; $j++) { 
						$titikX = $jejak[$j];
						$titikselanjutnya = -1;
						$taueta = [];
						for ($k=0; $k < $jumlahtitik; $k++) { 
							array_push($taueta, 0);
						}
						$jumlahtaueta = 0;
						for ($k=0; $k < count($taueta); $k++) { 
							if ($k == $titikX or $titikterpakai[$k] == 1) {
								$taueta[$k] = 0;
							}
							else {
								if ($jarak[$titikX][$j] != 0){
									$taueta[$k] = (($feromon[$titikX][$k])**$alpha)*((1/$jarak[$titikX][$j])**$beta);
								}
								else{
									$taueta[$k] = 0;
								}
								if ($taueta[$k] < 0.0001) {
									$taueta[$k] = 0.0001;
								}
							}
							$jumlahtaueta += $taueta[$k];
						}
						$probabilitas = [];
						for ($k=0; $k < $jumlahtitik; $k++) { 
							array_push($probabilitas, 0);
						}
						for ($k=0; $k < count($probabilitas); $k++) { 
							$probabilitas[$k] = $taueta[$k] / $jumlahtaueta;
						}
						$nilaikumulatif = [];
						for ($k=0; $k < count($probabilitas)+1; $k++) { 
							array_push($nilaikumulatif, 0);
						}
						for ($k=0; $k < count($probabilitas); $k++) { 
							$nilaikumulatif[$k+1] = $nilaikumulatif[$k] + $probabilitas[$k];
						}

						$p = rand_float();

						for ($k=0; $k < count($nilaikumulatif)-1; $k++) { 
							if ($p >= $nilaikumulatif[$k] and $p < $nilaikumulatif[$k+1]) {
								$titikselanjutnya = $k;
								$break;
							}
						}
						$jejak[$j+1] = $titikselanjutnya;
						$titikterpakai[$titikselanjutnya] = 1;
					}
					$semut[$i] = $jejak;
				}
				return $semut;
			}
			function updateFeromon($feromon, $semut, $jarak, $rho, $Q){
				for ($i=0; $i < count($feromon); $i++) { 
					for ($j=$i+1; $j < count($feromon); $j++) { 
						for ($k=0; $k < count($semut); $k++) { 
							$jarakterpendek = cariJarakTerpendek($semut[$k], $jarak);
							$faktorpengecil = (1 - $rho) * $feromon[$i][$j];
							$faktorpembesar = 0;
							if (isBersebelahan($i, $j, $semut[$k]) == 1) {
								$faktorpembesar = $Q / $jarakterpendek;
							}
							$feromon[$i][$j] = $faktorpengecil + $faktorpembesar;
							if ($feromon[$i][$j] < 0.0001) {
								$feromon[$i][$j] = 0.0001;
							}
							elseif ($feromon[$i][$j] > 100000) {
								$feromon[$i][$j] = 100000;
							}
							$feromon[$j][$i] = $feromon[$i][$j];
						}
					}
				}
				return $feromon;
			}
			function isBersebelahan($titikX, $titikY, $jejak){
				$indexterakhir = count($jejak);
				$idx = 0;
				for ($i=0; $i < count($jejak); $i++) { 
					if ($jejak[$i] == $titikX) {
						$idx = $i;
					}
				}
				if ($idx == 0 and $jejak[1] = $titikY) {
					return 1;
				}
				elseif ($idx == 0 and $jejak[$indexterakhir] == $titikY) {
					return 1;
				}
				elseif ($idx == 0) {
					return 0;
				}
				elseif ($idx == $indexterakhir and $jejak[$indexterakhir-1] == $titikY) {
					return 1;
				}
				elseif ($idx == $indexterakhir and $jejak[0] == $titikY) {
					return 1;
				}
				elseif ($idx == $indexterakhir) {
					return 0;
				}
				elseif ($jejak[$idx-1] == $titikY) {
					return 1;
				}
				elseif ($jejak[$idx] == $titikY) {
					return 1;
				}
				else{
					return 0;
				}
			}
			$alpha = 3;
			$beta = 2;
			$rho = 0.01;
			$Q = 2.0;
			$jumlahtitik = 8;
			$jumlahsemut = 10;
			$maxiterasi = 500;
			$koordinat = [
				[-0.0605740, 109.3440730],
				[-0.0303198, 109.3337121],
				[-0.0734995, 109.2992811],
				[-0.0284640, 109.3247400],
				[-0.0098750, 109.2980110],
				[-0.1078960, 109.3349880],
				[-0.0597080, 109.3597230],
				[-0.0437840, 109.3123000]
			];
			$jarak = [
				[0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0]
			];
			for ($i=0; $i < count($jarak); $i++) { 
				for ($j=0; $j < count($jarak[$i]); $j++) { 
					$jarak[$i][$j] = cariJarak($koordinat[$i], $koordinat[$j]);
				}
			}
			echo "Jarak antar titik: <br>";
			echo "<table class=outline>";
				echo "<tr class=bg1>";
					echo "<td class=bg2>Titik</td>";
					echo "<td>Sekolah</td>";
					for ($i=1; $i < count($jarak); $i++) {
						echo "<td>Siswa ".($i)."</td>";
					}
				echo "</tr>";
			for ($i=0; $i < count($jarak); $i++) {
				echo "<tr>";
				if ($i==0){
					echo "<td class=bg1>Sekolah</td>";
				}
				else{
					echo "<td class='bg1 pl pr'>Siswa ".($i)."</td>";
				}
				for ($j=0; $j < count($jarak[$i]); $j++) {
					echo "<td class='pl pr'>";
					echo $jarak[$i][$j];
					echo "</td>";
				}
				echo "</tr>";
			}
			echo "</table>";
			echo "<br>";
			echo "Parameter yang digunakan:<br>";
			echo "<table class='outline'>";
				echo "<tr class=bg1>";
					echo "<td>α</td>";
					echo "<td>β</td>";
					echo "<td>ρ</td>";
					echo "<td>Q</td>";
					echo "<td>N</td>";
					echo "<td>i</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td class='pl pr'>$alpha</td>";
					echo "<td class='pl pr'>$beta</td>";
					echo "<td class='pl pr'>$rho</td>";
					echo "<td class='pl pr'>$Q</td>";
					echo "<td class='pl pr'>$jumlahsemut</td>";
					echo "<td class='pl pr'>$maxiterasi</td>";
				echo "</tr>";
			echo "</table><br>";
			$semut = [
				[0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0]
			];
			for ($i=0; $i < $jumlahsemut; $i++) { 
				$titikawal = 0;
				$jejak = [0, 1, 2, 3, 4, 5, 6, 7];
				for ($j=0; $j < $jumlahtitik; $j++) { 
					$r = rand($j, $jumlahtitik-1);
					$tmp = $jejak[$r];
					$jejak[$r] = $jejak[$j];
					$jejak[$j] = $tmp;
				}
				$idx = 0;
				for ($j=0; $j < count($jejak); $j++) { 
					if ($jejak[$j] == $titikawal) {
						$idx = $j;
					}
				}
				$temp = $jejak[0];
				$jejak[0] = $jejak[$idx];
				$jejak[$idx] = $temp;
				$semut[$i] = $jejak;
			}
			echo "<table>";
			for ($i=0; $i < count($semut); $i++) { 
				echo "<tr>";
					echo "<td class=la>";
					echo "Semut ".($i+1);
					echo "</td>";
					echo "<td class=pr>:</td>";
					for ($j=0; $j < count($semut[$i]); $j++) {
						echo "<td class=pr>";
						if ($semut[$i][$j]==0){
							echo "S ";
						}
						else{
							echo $semut[$i][$j];
						}
						echo "</td>";
					}
					echo "<td class=la>";
					echo "Jarak: ".cariJarakTerpendek($semut[$i], $jarak)." km";
					echo "</td>";
				echo "</tr>";
			}
			echo "</table>";
			$jejakterbaik = cariJejakTerbaik($semut, $jarak);
			$jarakterpendek = cariJarakTerpendek($jejakterbaik, $jarak);

			echo "Jejak terbaik awal: ";
			foreach ($jejakterbaik as $key) {
				if ($key==0){
					echo "S ";
				}
				else{
					echo $key." ";
				}
			}
			echo "<br>Jarak terpendek untuk jejak awal mula: $jarakterpendek km<br>";
			$feromon = [
				[0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0]
			];

			for ($i=0; $i < count($feromon); $i++) { 
				for ($j=0; $j < count($feromon[$i]); $j++) { 
					$feromon[$i][$j] = 0.01;
				}
			}

			echo "<br>Pencarian jalur terpendek: <br>";
			$n = 0;
			while ($n < $maxiterasi) {
				$semut = updateSemut($semut, $feromon, $jarak, $alpha, $beta);
				$feromon = updateFeromon($feromon, $semut, $jarak, $rho, $Q);
				$jejakterbaiknow = cariJejakTerbaik($semut, $jarak);
				$jarakterpendeknow = cariJarakTerpendek($jejakterbaiknow, $jarak);
				if ($jarakterpendeknow < $jarakterpendek) {
					$jarakterpendek = $jarakterpendeknow;
					$jejakterbaik = $jejakterbaiknow;
					echo "Jejak terbaik terbaru: ";
					foreach ($jejakterbaik as $key) {
						if ($key==0){
							echo "S ";
						}
						else{
							echo $key." ";
						}
					}
					echo "(jarak: ".$jarakterpendek." km) ditemukan pada iterasi ".($n+1)."<br>";
				}
				$n++;
			}
			echo "<br>Jejak terbaik yang ditemukan: ";
			foreach ($jejakterbaik as $key) {
				if ($key==0){
					echo "S ";
				}
				else{
					echo $key." ";
				}
			}
			echo "<br>Jarak dari jejak terbaik yang ditemukan: ".$jarakterpendek." km<br>";
		?>
	</body>
</html>