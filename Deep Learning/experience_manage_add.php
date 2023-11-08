<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

use Gibbon\Forms\Form;
use Gibbon\Module\DeepLearning\Domain\EventGateway;

if (isActionAccessible($guid, $connection2, '/modules/Deep Learning/experience_manage_add.php') == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    // Proceed!
    $page->breadcrumbs
        ->add(__m('Manage Experiences'), 'experience_manage.php')
        ->add(__m('Add Experience'));

    if (isset($_GET['editID'])) {
        $page->return->setEditLink($session->get('absoluteURL').'/index.php?q=/modules/Deep Learning/experience_manage_edit.php&deepLearningExperienceID='.$_GET['editID']);
    }

    $eventGateway = $container->get(EventGateway::class);

    $form = Form::create('major', $session->get('absoluteURL').'/modules/'.$session->get('module').'/experience_manage_addProcess.php');

    $form->addHiddenValue('address', $session->get('address'));

    $events = $eventGateway->selectEventsBySchoolYear();

    // DETAILS
    $form->addRow()->addHeading(__('Basic Details'));

    $row = $form->addRow();
        $row->addLabel('deepLearningEventID', __('Event'));
        $row->addSelect('deepLearningEventID')->fromResults($events, 'groupBy')->required();

    $row = $form->addRow();
        $row->addLabel('name', __m('Experience Name'))->description(__m('Must be unique within this Deep Learning event.'));
        $row->addTextField('name')->required()->maxLength(90);

    $row = $form->addRow();
        $row->addLabel('status', __('Status'));
        $row->addSelect('status')->fromArray(['Draft' => __m('Draft'), 'Published' => __m('Published')])->required();

    // ENROLMENT
    $form->addRow()->addHeading(__('Enrolment'));

    $row = $form->addRow();
        $row->addLabel('cost', __('Cost'))->description(__m('Leave empty to not display a cost.'));
        $row->addCurrency('cost')->maxLength(10);

    $row = $form->addRow();
        $row->addLabel('enrolmentMin', __('Minimum Enrolment'))->description(__m('Experience should not run below this number of students.'));
        $row->addNumber('enrolmentMin')->onlyInteger(true)->minimum(0)->maximum(999)->maxLength(3)->required();

    $row = $form->addRow();
        $row->addLabel('enrolmentMax', __('Maximum Enrolment'))->description(__('Enrolment should not exceed this number of students.'));
        $row->addNumber('enrolmentMax')->onlyInteger(true)->minimum(0)->maximum(999)->maxLength(3)->required();


    // DISPLAY
    $form->addRow()->addHeading(__('Display'));

    $row = $form->addRow();
        $row->addLabel('headerImage', __m('Header Image'))->description(__m('A header image to display on the experience page.'));
        $row->addFileUpload('headerImageFile')->accepts('.jpg,.jpeg,.gif,.png');

    $row = $form->addRow();
        $col = $row->addColumn()->setClass('');
        $col->addLabel('description', __('Description'));
        $col->addEditor('description', $guid);

    $row = $form->addRow()->addClass('tags');
        $col = $row->addColumn();
        $col->addLabel('tags', __('Tags'));
        $col->addFinder('tags')
            ->setParameter('hintText', __('Type a tag...'))
            ->setParameter('allowFreeTagging', true);

    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    echo $form->getOutput();
}
