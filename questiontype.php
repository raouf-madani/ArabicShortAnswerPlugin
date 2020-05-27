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

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/arabicanswer/question.php');


/**
 * The arabicanswer question type.
 *
 * @copyright  2019 Snoussi El Hareth & Madani Abderraouf

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_arabicanswer extends question_type {

    /*******************************************************************************************************************************************/
 /**
     * Move all the files belonging to this question from one context to another.
     * @param int $questionid the question being moved.
     * @param int $oldcontextid the context it is moving from.
     * @param int $newcontextid the context it is moving to.
     */
    /*Assure le deplacement de tous les fichier appartenant à cette question d'un Contexte à un autre */
    public function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
    }
    
        /*******************************************************************************************************************************************/

 /**
     * Delete all the files belonging to this question.
     * @param int $questionid the question being deleted.
     * @param int $contextid the context the question is in.
     */
    /*
Supprimer tous les fichiers appartenant à cette question*/
    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_hints($questionid, $contextid);
    }
    
        /*******************************************************************************************************************************************/

    /*Ajouter la question a la base de données*/
    public function save_question_options($question) {
         global $DB;
        $result = new stdClass();

        /*mettre a jour ou ajouter la reponse dans la table question_answers*/
        $context = $question->context;
        $oldanswers = $DB->get_records('question_answers',
                array('question' => $question->id), 'id ASC');

            // Update an existing answer if possible.
            //Mettre a jour une reponse si c'est possible
            $answer = array_shift($oldanswers);
            if (!$answer) {
               
                $answer = new stdClass();
                $answer->question = $question->id;
                $answer->answer = '';
                $answer->feedback = '';
                $answer->id = $DB->insert_record('question_answers', $answer);
            }
            
           /************************************************************************************************************/
            /**
     * Return $answer, filling necessary fields for the question_answers table.
     *
     * The questions using question_answers table may want to overload this. Default code will work
     * for shortanswer and similar question types.
     * @param stdClass $answer Object to save data.
     * @param object $questiondata This holds the information from the question editing form or import.
     * @param int $key A key of the answer in question.
     * @param object $context needed for working with files.
     * @return $answer answer with filled data.
     */
        //creer une nouvelle question
        $answer->answer   = $question->answer;
        $answer->fraction = 1;
        $answer->feedback = '';
        $answer->feedbackformat = '';
        /*************************************************************************************************************/
            
            $DB->update_record('question_answers', $answer);
            

        // Delete any left over old answer records.
        $fs = get_file_storage();
        foreach ($oldanswers as $oldanswer) {
            $fs->delete_area_files($context->id, 'question', 'answerfeedback', $oldanswer->id);
            $DB->delete_records('question_answers', array('id' => $oldanswer->id));
        }
        
        // Save question options in question_arabicanswer table.
        //Ajouter la question a la table  question_arabicanswer 
        if ($options = $DB->get_record('qtype_arabicanswer_options', array('questionid' => $question->id))) {
            // No need to do anything, since the answer IDs won't have changed
            // But we'll do it anyway, just for robustness.
 
            $DB->update_record('qtype_arabicanswer_options', $options);
        } else {
            $options = new stdClass();
            $options->questionid  = $question->id;
            
            $DB->insert_record('qtype_arabicanswer_options', $options);
        }

      
        $this->save_hints($question);
    }
    /*******************************************************************************************************************************************/
    
    /**
     * Initialise the common question_definition fields.
     * @param question_definition $question the question_definition we are creating.
     * @param object $questiondata the question data loaded from the database.
     */
    protected function initialise_question_instance(question_definition $question, $questiondata) {
        //initialiser les champs commun des questions par exemple(temps de création , categorie,id du context ,feedback general ...)
        parent::initialise_question_instance($question, $questiondata);
        /**
     * Initialise question_definition::answers field.
     * @param question_definition $question the question_definition we are creating.
     * @param object $questiondata the question data loaded from the database.
     * 
     */
        //initialiser la réponse modele(feedback , answerformat,feedbackformat...)
        $this->initialise_question_answers($question, $questiondata);
        
    }
    
        /*******************************************************************************************************************************************/
    /* /**
     * This method should return all the possible types of response that are
     * recognised for this question.
     **/
    //Récupere toutes les réponses possible a cette question(dans ce type il n'y a qu'une seule)
    /*public function get_possible_responses($questiondata) {
     $responses = array();

        $starfound = false;
        foreach ($questiondata->options->answers as $aid => $answer) {
            $responses[$aid] = new question_possible_response($answer->answer,
                    $answer->fraction);
            if ($answer->answer === '*') {
                $starfound = true;
            }
        }

        if (!$starfound) {
            $responses[0] = new question_possible_response(
                    get_string('didnotmatchanyanswer', 'question'), 0);
        }

        $responses[null] = question_possible_response::no_response();

        return array($questiondata->id => $responses);
    }
    
    */
        /*******************************************************************************************************************************************/
/**
     * Deletes the question-type specific data when a question is deleted.
     * @param int $question the question being deleted.
     * @param int $contextid the context this quesiotn belongs to.
     */
    /*Supprimer la question de toutes les tables(La table specifique et la table generale)*/
 public function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records('qtype_arabicanswer_options', array('questionid' => $questionid));

            
        parent::delete_question($questionid, $contextid);

}
    /*******************************************************************************************************************************************/
     /**
     * Loads the question type specific options for the question.
     */
    /*Récuperer les données de la question */
    public function get_question_options($question) {
      global $DB, $OUTPUT;
     if (!isset($question->options)) {
            $question->options = new stdClass();
        }
        
        $question->options = $DB->get_record('qtype_arabicanswer_options',
                array('questionid' => $question->id));
        $question->options->answers = $DB->get_records('question_answers',
        array('question' => $question->id), 'id ASC');
        return true;
       
    }
    /*******************************************************************************************************************************************/
    
    
}
    
    ?>
