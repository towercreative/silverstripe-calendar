    <% if $SearchEnabled %>
        <ul class="CalendarPageSearch">
        <li class="search">
            <form id="EventSearch" action="{$Link}search">
                <input type="text" name="q" id="EventSearchQuery" placeholder="$SearchQuery" />
                <input type="submit" value="search" />
            </form>
        </li>
        </ul>
    <% end_if %>

    <ul class="CalendarPageMenu">
	<li class="calendarview <% if $CurrentMenu == 'calendarview'%>current<% end_if %>">
		<a href="{$CalendarViewLink}" class="btn">Calendar View</a>
	</li>
	<li class="eventlist <% if $CurrentMenu == 'eventlist'%>current<% end_if %>">
		<a href="{$EventListLink}" class="btn">List View</a>
	</li>
	<% if $RegistrationsEnabled %>
		<li class="registerableevents <% if $CurrentMenu == 'eventregistration'%>current<% end_if %>">
			<a href="{$Link}eventregistration/">Event Registration</a>
		</li>
	<% end_if %>
</ul>
