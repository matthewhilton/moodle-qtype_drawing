﻿<?php
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
 * drawing question renderer class.
 *
 * @package	qtype
 * @subpackage drawing
 * @copyright ETHZ LET <amr.hourani@id.ethz.ch>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Generates the output for drawing questions.
 *
 * @copyright  ETHZ LET <amr.hourani@id.ethz.chh>
 * @license	http://opensource.org/licenses/BSD-3-Clause
 */

define('AJAX_SCRIPT', true);
require_once ('../../../config.php');

$id = required_param('id', PARAM_INT);
$sesskey = required_param('sesskey', PARAM_RAW);
$stid = required_param('stid', PARAM_INT);

$attemptid = required_param('attemptid', PARAM_RAW_TRIMMED);
if(!isloggedin() || !confirm_sesskey()){
  echo json_encode(array('result' => 'Session lost.'));
  die;
}

require_once("../../../question/type/questiontypebase.php");
if(!$question = question_bank::load_question_data($id)){
  echo json_encode(array('result' => 'Question attempt not found'));
  die;
}
if (!has_capability('mod/quiz:grade', context::instance_by_id($question->contextid)) ) {
  echo json_encode(array('result' => 'No permission'));
  die;
}
if(!$fhd = $DB->get_record('qtype_drawing', array('questionid'=> $id)) ){
  echo json_encode(array('result' => 'Question not found'));
  die;
}

$result = '';
$fields = array('questionid' =>  $question->id, 'attemptid' => $attemptid, 'annotatedfor' => $stid, 'annotatedby' => $USER->id);
if ($annotations = $DB->get_record('qtype_drawing_annotations', $fields)){
      $result = userdate($annotations->timemodified).' ('.get_string('ago', 'core_message', format_time(time() + 1 - $annotations->timemodified)).')';
}

echo json_encode(array('result' => $result, 'userid' => $USER->id));
