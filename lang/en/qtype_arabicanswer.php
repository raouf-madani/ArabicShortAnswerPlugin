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

$string['pluginname'] = 'Semantic Short Answer';
$string['pluginname_help'] = 'Just enter the model answer and the mark will be automatically assigned';
$string['pluginname_link'] = 'question/type/arabicanswer';
$string['pluginnameadding'] = 'Adding a arabicanswer question';
$string['pluginnameediting'] = 'Editing a arabicanswer question';
$string['pluginnamesummary'] = 'A arabicanswer question is a question that automatically assign student responses based on a semantic similarity with the teacher\'s model response .';
$string['test'] = 'test';
$string['pleaseenterananswer'] = 'Please enter an answer.';
$string['answer'] = 'Answer: {$a}';
$string['correctansweris'] = 'The correct answer is: {$a}';
$string['modelanswer'] = 'Model answer';
$string['modelansweris'] = 'The model answer is : {$a}';
$string['provideanswer'] = 'You should provide the model answer to this question which student answers will be compared to';
$string['verygood'] ='Very Good !';
$string['good'] ='Good !';
$string['satisfactory'] ='satisfactory';
$string['average'] ='Average';
$string['poor'] ='poor';
$string['failed'] ='Failed';