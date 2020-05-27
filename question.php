<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines the editing form for the arabicanswer question type.
 *
 * @package    qtype
 * @subpackage arabicanswer
 * @copyright  2019 Snoussi El Hareth & Madani Abderraouf
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/question/type/questionbase.php');

/**
 * Represents a arabicanswer question.
 *
 * @copyright  2019 Snoussi El Hareth & Madani Abderraouf

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/*Construct a question type instance everytime we create a new question*/
/*Instancier une question apres chaque creation d'une question dans la banque a question*/
class qtype_arabicanswer_question extends question_graded_automatically {
    public function __construct() {
        parent::__construct();
    }
   
/********************************************************************************************/
/**
     * What data may be included in the form submission when a student submits
     * this question in its current state?
     *
     * This information is used in calls to optional_param. The parameter name
     * has {@link question_attempt::get_field_prefix()} automatically prepended.
     *
     * @return array|string variable name => PARAM_... constant, or, as a special case
     *      that should only be used in unavoidable, the constant question_attempt::USE_RAW_DATA
     *      meaning take all the raw submitted data belonging to this question.
     */
    public function get_expected_data() {

        return array('answer' => PARAM_RAW);
    }

/********************************************************************************************/

    /**
     * Produce a plain text summary of a response.
     * @param $response a response, as might be passed to {@link grade_response()}.
     * @return string a plain text summary of that response, that could be used in reports.
     */
   /* Envoyer la réponse soumise par l'etudiant vers la base de données*/
    /* Envoyer la réponse soumise par l'etudiant vers la base de données a afficher dans  les rapports */
    public function summarise_response(array $response) {
        if (isset($response['answer'])) {
          
            return $response['answer'];
        } else {
            return null;
        }
    
    }

/********************************************************************************************/

    
    /**
     * Used by many of the behaviours, to work out whether the student's
     * response to the question is complete. That is, whether the question attempt
     * should move to the COMPLETE or INCOMPLETE state.
     *
     * @param array $response responses, as returned by
     *      {@link question_attempt_step::get_qt_data()}.
     * @return bool whether this response is a complete answer to this question.
     */
    /*Vérifier si l'etat de la reponse étudiant est complet ou incomplet*/
    public function is_complete_response(array $response) {
        return array_key_exists('answer', $response) &&
                ($response['answer'] || $response['answer'] === '0');
    }


/********************************************************************************************/
    
    public function get_validation_error(array $response) {
       
        /**
     * Use by many of the behaviours to determine whether the student
     * has provided enough of an answer for the question to be graded automatically,
     * or whether it must be considered aborted.
     *
     * @param array $response responses, as returned by
     *      {@link question_attempt_step::get_qt_data()}.
     * @return bool whether this response can be graded.
     */
        /**
     * In situations where is_gradable_response() returns false, this method
     * should generate a description of what the problem is.
     * @return string the message.
     */
        /*
        déterminer si la reponse de l'etudiant est valide ou pas
        si ça retourne false il faut génerer une description pour ce probleme 
        ça retourne false que lorsque on ajoute une condition a la validation des reponses
        */
        if ($this->is_gradable_response($response)) {
            return '';
        }
        return get_string('pleaseenterananswer', 'qtype_arabicanswer');
    }


    
/********************************************************************************************/
     /**
     * Use by many of the behaviours to determine whether the student's
     * response has changed. This is normally used to determine that a new set
     * of responses can safely be discarded.
     *
     * @param array $prevresponse the responses previously recorded for this question,
     *      as returned by {@link question_attempt_step::get_qt_data()}
     * @param array $newresponse the new responses, in the same format.
     * @return bool whether the two sets of responses are the same - that is
     *      whether the new set of responses can safely be discarded.
     */
    /*
    Si l'etudiant change la reponse et qu'elle est identique a sa reponse précedente la nouvelle reponse peut etre écarté et ne pas pris en considération 
    */
    public function is_same_response(array $prevresponse, array $newresponse) {
        return question_utils::arrays_same_at_key_missing_is_blank(
                $prevresponse, $newresponse, 'answer');
    }

/********************************************************************************************/
    /**
     * What data would need to be submitted to get this question correct.
     * If there is more than one correct answer, this method should just
     * return one possibility. If it is not possible to compute a correct
     * response, this method should return null.
     *
     * @return array|null parameter name => value.
     */
    /*retourner la reponse modéle de l'enseignant */
    public function get_correct_response() {
        foreach($this->answers as $cle => $valeur){
        $answer = $this->answers[$cle]->answer;
        
        }
        
        return array('answer' => $answer);

    }
   
  
    


/********************************************************************************************/

/**************************************************************************************/
 /*Assurer la Sécurité des réponses soumises par l'étudiant */
 public static function checkInput($var)
                    {
         
                         $var= trim($var);
                         $var= stripslashes($var);
                         $var= htmlspecialchars($var);

                         return $var;
                    } 
 
/**************************************************************************************
     * Normalise a UTf-8 string to FORM_C, avoiding the pitfalls in PHP's
     * normalizer_normalize function.
     * @param string $string the input string.
     * @return string the normalised string.
     */
    protected static function safe_normalize($string) {
        if ($string === '') {
            return '';
        }

        if (!function_exists('normalizer_normalize')) {
            return $string;
        }

        $normalised = normalizer_normalize($string, Normalizer::FORM_C);
        if (is_null($normalised)) {
            // An error occurred in normalizer_normalize, but we have no idea what.
            debugging('Failed to normalise string: ' . $string, DEBUG_DEVELOPER);
            return $string; // Return the original string, since it is the best we have.
        }

        return $normalised;
    }
 
 

/********************************************************************************************/
/**
     * Grade a response to the question, returning a fraction between
     * get_min_fraction() and get_max_fraction(), and the corresponding {@link question_state}
     * right, partial or wrong.
     * @param array $response responses, as returned by
     *      {@link question_attempt_step::get_qt_data()}.
     * @return array (float, integer) the fraction, and the state.
     */
    /*Noter la réponse de l'étudiant et retourner la note et l'etat de la question*/
    public function grade_response(array $response) {

         if (!array_key_exists('answer', $response) || is_null($response['answer'])) {
            return false;
        }
        
        foreach($this->answers as $cle => $valeur){
        $answer = $this->answers[$cle]->answer;
        
        }
      
        
        $donnee= self::safe_normalize($answer);
        $data =  str_replace(array("\r", "\n"), " ", self::safe_normalize($response['answer']));
        $curlvar= curl_init(); //Initialise the cURL var
        curl_setopt($curlvar,CURLOPT_RETURNTRANSFER, 1); //Get the response from cURL
        curl_setopt($curlvar,CURLOPT_URL,'http://harethraouf.pythonanywhere.com/?donnee='.self::checkInput($donnee).'&data='.self::checkInput($data)); //Set the Url
        $content= (string) self::checkInput(curl_exec($curlvar));
        curl_close($curlvar);
        $fraction = (float)$content;
        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

    
/********************************************************************************************/
   
   

    
    
    
}
