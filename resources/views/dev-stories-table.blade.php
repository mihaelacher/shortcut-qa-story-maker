<table id="devStoriesTable" class="dataTable display hover order-column row-border stripe cell-border ">
    <thead>
        <tr>
            <th style="max-width: 40px; text-align: center;">
                <input type="checkbox" class="js-stories-checkbox-all" @if(!$stories || !count($stories)) disabled @endif>
            </th>
            <th title="QA Master Story">M</th>
            <th title="QA Scenario Story">S</th>
            <th title="QA Test Story">T</th>
            <th title="QA Automation Story">A</th>
            <th>STORY NAME</th>
            <th>OWNER</th>
            <th>STORY TYPE</th>
            <th>STORY TEAM</th>
            <th>STORY LABELS</th>
            <th>STORY STATE</th>
        </tr>
    </thead>
    <tbody>
        @foreach($stories ?? [] as $story)
        <tr>
            <td style="text-align: center">
                <input type="checkbox" class="js-story-checkbox" data-story-id="{{$story['id']}}">
            </td>
            <td title="QA Master Story" style="text-align: center">
                <input data-creator-name="{{ \App\Services\Creators\QAMasterStoryCreator::getCreatorName() }}" data-story-id="{{$story['id']}}" type="checkbox" checked>
            </td>
            <td title="QA Scenario Story" style="text-align: center">
                <input data-creator-name="{{ \App\Services\Creators\QAScenarioStoryCreator::getCreatorName() }}" data-story-id="{{$story['id']}}" type="checkbox" checked>
            </td>
            <td title="QA Test Story" style="text-align: center">
                <input data-creator-name="{{ \App\Services\Creators\QATestStoryCreator::getCreatorName() }}" data-story-id="{{$story['id']}}" type="checkbox" checked>
            </td>
            <td title="QA Automation Story" style="text-align: center">
                <input data-creator-name="{{ \App\Services\Creators\QAAutomationStoryCreator::getCreatorName() }}" data-story-id="{{$story['id']}}" type="checkbox" checked>
            </td>
            <td> <a href="https://app.shortcut.com/shkolo/story/{{$story['id']}}" target="_blank"> {{ $story['name'] }} </a></td>
            <td title="Owner" style="text-align: center">
                <select data-story-id="{{$story['id']}}">
                    @foreach($ninjaOwners ?? [] as $id => $name)
                        <option value="{{$id}}" @if(in_array($id, $story['owner_ids'])) selected @endif>{{$name}}</option>
                    @endforeach
                </select>
            </td>
            <td> {{ $story['type'] }} </td>
            <td> {{ $story['group'] }} </td>
            <td> {{ $story['labels'] }} </td>
            <td> {{ $story['state'] }} </td>
        </tr>
        @endforeach
    </tbody>
</table>
