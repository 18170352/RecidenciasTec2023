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
 * Steps definitions for marking guides.
 *
 * @package   gradingform_guide
 * @category  test
 * @copyright 2015 Jun Pataleta
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 require_once(__DIR__ . '/../../../../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException,
    Behat\Mink\Exception\ExpectationException as ExpectationException;

class behat_gradingform_guide extends behat_base {

    public function i_define_the_following_marking_guide(TableNode $guide) {
        $steptableinfo = '| Criterion name | Description for students | Description for markers | Maximum score |';

        if ($criteria = $guide->getHash()) {
            $addcriterionbutton = $this->find_button(get_string('addcriterion', 'gradingform_guide'));

            foreach ($criteria as $index => $criterion) {
                // Make sure the criterion array has 4 elements.
                if (count($criterion) != 4) {
                    throw new ExpectationException(
                        'The criterion definition should contain name, description for students and markers, and maximum points. ' .
                        'Please follow this format: ' . $steptableinfo,
                        $this->getSession()
                    );
                }
                // Rest of the code to interact with the UI and define the marking guide.
            }
        }
    }
}

/**
 * Set the field value for the given guide field.
 *
 * @param string $field The field name.
 * @param string $value The value to set.
 * @param bool $visible Whether the field is initially visible.
 */
private function set_guide_field_value($field, $value, $visible = true) {
    $fieldselector = $this->get_selectors('guide', $field);
    $fieldelement = $this->assert_session()->elementExists('css', $fieldselector);

    // Check if the field is visible, if not, click on it to make it visible.
    if (!$fieldelement->isVisible() && $visible) {
        $fieldvisibilitytoggle = $this->get_selectors('guide', 'showhide');
        $this->getSession()->getPage()->find('css', $fieldvisibilitytoggle)->click();
    }

    // Set the field value.
    $this->getSession()->getPage()->fillField($fieldselector, $value);

    // Optionally, you may want to add some assertions or additional interactions here.

    // Check if the field is no longer visible, if not, click on it to hide it again.
    if ($fieldelement->isVisible() && $visible) {
        $fieldvisibilitytoggle = $this->get_selectors('guide', 'showhide');
        $this->getSession()->getPage()->find('css', $fieldvisibilitytoggle)->click();
    }
}

    /**
     * Defines the marking guide with the provided data, following marking guide's definition grid cells.
     *
     * This method fills the table of frequently used comments of the marking guide definition form.
     * The provided TableNode should contain one row for each frequently used comment.
     * Each row contains:
     * # Comment
     *
     * Works with both JS and non-JS.
     *
     * @When /^I define the following frequently used comments:$/
     * @throws ExpectationException
     * @param TableNode $commentstable
     */
    public function i_define_the_following_frequently_used_comments(TableNode $commentstable) {
        $steptableinfo = '| Comment |';

        if ($comments = $commentstable->getRows()) {
            $addcommentbutton = $this->find_button(get_string('addcomment', 'gradingform_guide'));

            foreach ($comments as $index => $comment) {
                // Make sure the comment array has only 1 element.
                if (count($comment) != 1) {
                    throw new ExpectationException(
                        'The comment cannot be empty. Please follow this format: ' . $steptableinfo,
                        $this->getSession()
                    );
                }

                // On load, there's already a comment template ready.
                $commentfieldvisible = false;
                if ($index > 0) {
                    // So if the index is greater than 0, we click the Add frequently used comment button to add a new criterion.
                    $addcommentbutton->click();
                    $commentfieldvisible = true;
                }

                $commentroot = 'guide[comments][NEWID' . ($index + 1) . ']';

                // Set the field value for the frequently used comment.
                $this->set_guide_field_value($commentroot . '[description]', $comment[0], $commentfieldvisible);
            }
        }
    }

    /**
 * Performs grading of the student by filling out the marking guide.
 * Set one line per criterion and for each criterion set "| Criterion name | Points | Remark |".
 *
 * @When /^I grade by filling the marking guide with:$/
 *
 * @throws ExpectationException
 * @param TableNode $guide
 * @return void
 */
public function i_grade_by_filling_the_marking_guide_with(TableNode $guide) {

    $criteria = $guide->getRowsHash();

    $stepusage = '"I grade by filling the rubric with:" step needs you to provide a table where each row is a criterion' .
        ' and each criterion has 3 different values: | Criterion name | Number of points | Remark text |';

    // First element -> name, second -> points, third -> Remark.
    foreach ($criteria as $name => $criterion) {

        // We only expect the points and the remark, as the criterion name is $name.
        if (count($criterion) !== 2) {
            throw new ExpectationException($stepusage, $this->getSession());
        }

        // Numeric value here.
        $points = $criterion[0];
        if (!is_numeric($points)) {
            throw new ExpectationException($stepusage, $this->getSession());
        }

        $criterionid = 0;
        if ($criterionnamediv = $this->find('xpath', "//div[@class='criterionshortname'][text()='$name']")) {
            $criteriondivname = $criterionnamediv->getAttribute('name');
            // Criterion's name is of the format "advancedgrading[criteria][ID][shortname]".
            // So just explode the string with "][" as delimiter to extract the criterion ID.
            if ($nameparts = explode('][', $criteriondivname)) {
                $criterionid = $nameparts[1];
            }
        }

        if ($criterionid) {
            $criterionroot = 'advancedgrading[criteria]' . '[' . $criterionid . ']';

            $this->execute('behat_forms::i_set_the_field_to', array($criterionroot . '[score]', $points));

            $this->execute('behat_forms::i_set_the_field_to', array($criterionroot . '[remark]', $criterion[1]));
        }
    }
}

/**
 * Makes a hidden marking guide field visible (if necessary) and sets a value on it.
 *
 * @param string $name The name of the field
 * @param string $value The value to set
 * @param bool $visible
 * @return void
 */
protected function set_guide_field_value($name, $value, $visible = false) {
    // Fields are hidden by default.
    if ($this->running_javascript() && $visible === false) {
        $xpath = "//*[@name='$name']/following-sibling::*[contains(concat(' ', normalize-space(@class), ' '), ' plainvalue ')]";
        $textnode = $this->find('xpath', $xpath);
        $textnode->click();
    }

    // Set the value now.
    $field = $this->find_field($name);
    $field->setValue($value);
}
