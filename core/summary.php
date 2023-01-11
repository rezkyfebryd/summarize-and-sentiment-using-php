<?php

$model->load('summarize.ini'); // load the saved weights

class Summarizer {
	var $word_stats;
	var $stopwords;
	
	//Constructor
	function Summarizer(){
		global $default_stopwords;
		$this->word_stats = array();
		$this->stopwords = $default_stopwords; //see the end of this file
	}
	
	/**
	 * $percent - what percentage of text should be used as the summary (in sentences).
	 * $min_sentences - the minimum length of the summary.
	 * $max_sentences - the maximum length of the summary.
	 */
	function summary($value, $percent=0.1, $min_sentences=1, $max_sentences=2){
		$sentences = $this->sentence_tokenize($value);
		$sentence_bag = array();
		
		//function summary($text, $percent=0.2, $min_sentences=1, $max_sentences=0){
		//$sentences = $this->sentence_tokenize($text);
		//$sentence_bag = array();
		
		for($i=0; $i<count($sentences); $i++){
			$words = $this->word_tokenize($sentences[$i]);
			$word_stats = array();
			foreach ($words as $word){
				//skip stopwords
				if (in_array($word, $this->stopwords)) continue;
				//stem
				$word = PorterStemmer::Stem($word);
				//skip stopwords by stem
				if (in_array($word, $this->stopwords)) continue;
				
				//per-sentence word counts
				if (!isset($word_stats[$word])) {
					$word_stats[$word]=1;
				} else {
					$word_stats[$word]++;
				} 
				
				//global word counts
				if (!isset($this->word_stats[$word])) {
					$this->word_stats[$word]=1;
				} else {
					$this->word_stats[$word]++;
				} 
			}
			
			$sentence_bag[] = array(
				'sentence' => $sentences[$i],
				'word_stats' => $word_stats,
				'ord' => $i
			);
		}
		
		//sort words by frequency
		arsort($this->word_stats);
		//only consider top 20 most common words. Throw away the rest.
		$this->word_stats = array_slice($this->word_stats,0,10);
		
		for($i=0; $i<count($sentence_bag); $i++){
			$rating = $this->calculate_rating($sentence_bag[$i]['word_stats']);
			$sentence_bag[$i]['rating'] = $rating;
		}
		
		//Sort sentences by importance rating
		usort($sentence_bag, array(&$this, 'cmp_arrays_rating'));
		
		//How many sentences do we need?
		if ($max_sentences==0) $max_sentences = count($sentence_bag);
		$summary_count = min(
			$max_sentences, 
			max( 
				min($min_sentences, count($sentence_bag)) , 
				round($percent*count($sentence_bag))
			)
		);
		if ($summary_count<1) $summary_count = 1;
		
		//echo "Total sentences : ".count($sentence_bag).", summary : $summary_count\n";
		
		//Take the X highest rated sentences (from the end of the array)
		$summary_bag = array_slice($sentence_bag, -$summary_count);
				
		//Restore the original sentence order
		usort($summary_bag, array(&$this, 'cmp_arrays_ord'));
		
		$summary_sentences = array();
		foreach($summary_bag as $sentence){
			$summary_sentences[] = $sentence['sentence'];
		}
		
		return $summary_sentences;
	}
	
	function cmp_arrays_rating($a, $b){
		return $this->cmp_arrays($a, $b, 'rating');
	}
	
	function cmp_arrays_ord($a, $b){
		return $this->cmp_arrays($a, $b, 'ord');
	}
	
	function cmp_arrays($a, $b, $key){
		if (is_int($a[$key]) || is_float($a[$key])){
			return floatval($a[$key])-floatval($b[$key]);
		} else {
			return strcmp(strval($a[$key]), strval($b[$key]));
		}
	}
	
	function sentence_tokenize($value){
		//Splits text into sentences. Treats newlines as end-of-sentence markers, too.
		if (preg_match_all('/["\']*.+?([.?!\n\r]+["\']*\s+|$)/si', $value, $matches, PREG_SET_ORDER)){
			$rez = array();
			foreach ($matches as $match){
				array_push($rez, trim($match[0]));
			}
			return $rez;
		} else { 
			return array($text);
		}
	}
	
	function word_tokenize($sentence){
		//Splits text into words and also does some cleanup.
		$words = preg_split('/[\'\s\r\n\t$]+/', $sentence);
		$rez = array();
		foreach($words as $word){
			$word = preg_replace('/(^[^a-z0-9]+|[^a-z0-9]$)/i','', $word);
			$word = strtolower($word);
			if (strlen($word)>0)
				array_push($rez, $word);
		}
		return $rez;
	}
	
	function calculate_rating($sentence_words){
		//Very primitive. Needs to be improved.
		$rating = 0;
		foreach ($sentence_words as $word => $count){
			if (!isset($this->word_stats[$word])) continue;
			$word_rating = $count * $this->word_stats[$word];
			$rating += $word_rating;
		}
		return $rating;
	}

}

//List of common words from Open Text Summarizer
$default_stopwords = array(
'--', '-', 'a', 'ada', 'adalah', 'adanya', 'adapun', 'agak', 'agaknya', 'agar', 'akan', 'akankah', 'akhir', 'akhiri',
 'akhirnya', 'aku', 'akulah', 'amat', 'amatlah', 'anda', 'andalah', 'antar', 'antara', 'antaranya', 'apa', 'apaan',
 'been', 'apabila', 'apakah', 'apalagi', 'apatah', 'arti', 'artinya', 'asal', 'asalkan', 'atas', 'atau', 'ataukah', 'ataupun',
 'awal','awalnya', 'bagai', 'bagaikan', 'bagaimana', 'bagaimanakah', 'bagaimanapun', 'bagi', 'bagian', 'bahkan', 'bahwa', 'bahwasanya', 'baik',
 'bakal', 'bakalan', 'balik', 'banyak', 'bapak', 'baru', 'bawah', 'berapa', 'beberapa', 'begini', 'beginian',
 'beginikah', 'beginilah', 'begitu', 'begitukah', 'begitulah', 'begitupun', 'bekerja', 'belakang', 'belakangan', 'belum', 'belumlah', 'benar', 'benarkah', 'benarlah', 'berada',
 'berakhir', 'berakhirlah', 'berakhirnya', 'berapa', 'berapakah', 'berapalah', 'berapapun', 'berarti', 'berawal', 'berbagai', 'berdatangan', 'beri', 'berikan', 'berikut', 'berikutnya',
 'berjumlah','berkali-kali', 'berkata', 'berkehendak', 'berkeinginan', 'berkenan', 'berlalu', 'berlangsung', 'berlebihan', 'bermacam', 'bermacam-macam', 'bermaksud', 'bermula', 'bersama',
 'bersama-sama', 'bersiap', 'bersiap-siap', 'bertanya', 'bertanya-tanya', 'berturut', 'berturut-turut', 'bertutur', 'berujar', 'berupa', 'besar', 'betul', 'betul-betul', 'betulkah',
 'biasa', 'biasanya', 'biasalah', 'bila', 'bilakah', 'bisa', 'bisakah', 'boleh', 'bolehkah', 'bolehlah', 'buat', 'bukan', 'bukankah', 'bukanlah',
 'bukannya', 'bulan', 'bung', 'cara', 'caranya', 'cukup', 'cukupkah', 'cukuplah', 'cuma', 'dahulu', 'dalam', 'dan', 'dapat',
 'dari', 'daripada', 'datang', 'dekat', 'demi', 'demikian', 'demikianlah', 'dengan', 'depan', 'di', 'dia', 'diakhiri',
 'diakhirinya', 'dialah', 'diantara', 'diantaranya', 'diberi', 'diberikan', 'diberikannya', 'dibuat', 'dibuatnya', 'didapat', 'didatangkan',
 'digunakan', 'diibaratkan', 'diibaratkannya', 'diingat', 'diingatkan', 'diinginkan', 'dijawab', 'dijelaskan', 'dijelaskannya', 'dikarenakan', 'dikatakan', 'dikatakannya', 'dikerjakan',
 'diketahui', 'diketahuinya', 'dikira', 'dilakukan', 'dilalui', 'dilihat', 'dimaksud', 'dimaksudkan', 'dimaksudkannya', 'dimaksudnya', 'diminta', 'dimintai', 'dimisalkan',
 'dimulai', 'dimulailah', 'dimulainya', 'dimungkinkan', 'dini', 'dipastikan', 'diperbuat', 'diperbuatnya', 'dipergunakan', 'diperkirakan', 'diperlihatkan', 'diperlukan', 'diperlukannya', 'dipersoalkan',
 'dipertanyakan', 'dipunyai', 'diri', 'dirinya', 'disampaikan', 'disebut', 'disebutkan', 'disebutkannya', 'disini', 'disinilah', 'ditambahkan', 'ditandaskan', 'ditanya', 'ditanyai',
 'ditanyakan', 'ditegaskan', 'ditujukan', 'ditunjuk', 'ditunjuki', 'ditunjukkan', 'ditunjukkannya'  
 );
 

?>