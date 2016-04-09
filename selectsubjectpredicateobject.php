<?php
    /**
     * Making a SPARQL SELECT query
     *
     * This example creates a new SPARQL client, pointing at the
     * dbpedia.org endpoint. It then makes a SELECT query that
     * returns all of the countries in DBpedia along with an
     * english label.
     *
     * Note how the namespace prefix declarations are automatically
     * added to the query.
     *
     * @package    EasyRdf
     * @copyright  Copyright (c) 2009-2013 Nicholas J Humfrey
     * @license    http://unlicense.org/
     */

    set_include_path(get_include_path() . PATH_SEPARATOR . './easyrdf-0.9.0/lib/');
    require_once "./easyrdf-0.9.0/lib/EasyRdf.php";
//    require_once "./easyrdf-0.9.0/examples/html_tag_helpers.php";

    // Setup some additional prefixes for the Drupal Site
    EasyRdf_Namespace::set('schema', 'http://schema.org/');
    EasyRdf_Namespace::set('content', 'http://purl.org/rss/1.0/modules/content/');
    EasyRdf_Namespace::set('dc', 'http://purl.org/dc/terms/');
    EasyRdf_Namespace::set('foaf', 'http://xmlns.com/foaf/0.1/');
    EasyRdf_Namespace::set('og', 'http://ogp.me/ns#');
    EasyRdf_Namespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
    EasyRdf_Namespace::set('sioc', 'http://rdfs.org/sioc/ns#');
    EasyRdf_Namespace::set('sioct', 'http://rdfs.org/sioc/types#');
    EasyRdf_Namespace::set('skos', 'http://www.w3.org/2004/02/skos/core#');
    EasyRdf_Namespace::set('xsd', 'http://www.w3.org/2001/XMLSchema#');
    EasyRdf_Namespace::set('owl', 'http://www.w3.org/2002/07/owl#');
    EasyRdf_Namespace::set('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
    EasyRdf_Namespace::set('rss', 'http://purl.org/rss/1.0/');
    EasyRdf_Namespace::set('site', 'http://localhost/iksce/ns#');

   $sparql = new EasyRdf_Sparql_Client('http://localhost:8080/marmotta/sparql/');
?>

<?php

// Set debug to 1 for debugging
$dbg = 1;
?>

<?php

 // Perform SELECT query on RDF store to populate array for all triples with schema:isRelatedTo predicate
/*
 $result = $sparql->query(
     'SELECT DISTINCT ?s { ?s ?p ?o . }'
 );
// specify the arc2storepath below
 */
 $result = $sparql->query(
     'SELECT * {
  SERVICE <http://arc2storepath/sparql> {
  SELECT DISTINCT ?s { ?s ?p  ?o . }
    }
  }'
 );

// Initialize itermediary storage array for subject, predicate, and object from query with schema:isRelatedTo predicate
 $subarray = array();
 $secondarray = array();

// Populate the storage arrays including the schema:isRelatedTo predicate
 foreach ($result as $key => $value) {
     $subarray[$key] = $value->s;
 }

 $subarraymatches = array();
// filter to what you want with regex
 $subarraymatches = preg_grep('/http.*portal/i',$subarray);

/*
 foreach($subarraymatches as $k => $value) {
   echo ($subarraymatches[$k]);
 }
*/

// print_r($subarraymatches);

?>


<?php


//$subjechtvar = 'http://localhost/drupal-7.42/content/owen-paterson';
//$subject = '<'.$subjechtvar.'>';
 //$secondarray = array(array());
 $secondarray = array();

foreach($subarraymatches as $k => $value) {

$subjechtvar = strval($subarraymatches[$k]);
$subject = '<'.$subjechtvar.'>';


 // Perform SELECT query on RDF store to populate array for all triples with schema:isRelatedTo predicate
/*
 $result = $sparql->query(
     'SELECT DISTINCT ?p ?o { '.$subject.' ?p ?o . }'
 );
*/

// filter to what you want with regex
$result = $sparql->query(
    'SELECT * {
      SERVICE <http://arc2storepath/sparql> {
      SELECT DISTINCT ?p ?o { '.$subject.' ?p  ?o . }
      }
  }'
);



 $predarray = array();
 $objarray = array();

// Populate the storage arrays including the schema:isRelatedTo predicate
 foreach ($result as $i => $row) {
  //   array_push($subarray, $row->s);
     array_push($predarray, $row->p);
     array_push($objarray, $row->o);
 }

//print_r($objarray);
/*
foreach ($predarray as $i => $value) {
  echo $predarray[$i].' '.$objarray[$i];
}
*/
$thirdarray = array();

foreach($predarray as $i => $value) {
 // echo($predarray->uri);
  $thirdarray[strval($predarray[$i])] = strval($objarray[$i]);
}

/*
echo "The third array";
echo("\r\n");
foreach($thirdarray as $i => $value) {
  echo $thirdarray[$i];
}
*/
  //echo "=====\n";
  //var_dump($predarray);
  //echo "=====\n";

  foreach($objarray as $i => $value) {
  //  echo strval($subarraymatches[$k]).' '.strval($predarray[$i]).' '.strval($objarray[$i]);
  /*
    echo "k = $k, i = $i\n";
    var_dump(array("s"=>$subarraymatches[$k], "p" => $predarray[$i], "o" => $objarray[$i]));
    echo "\n";
  */
  // echo("\r\n");
  //
  $secondarray[strval($subarraymatches[$k])][strval($predarray[$i])] = strval($objarray[$i]);
 }

}

print_r($secondarray);


?>
