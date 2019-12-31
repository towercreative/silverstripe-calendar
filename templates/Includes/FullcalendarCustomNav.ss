<div id="FullcalendarCustomNav">
    MENU=$CurrentMenu
	<div class="fullcalendarCustomNavTop">
        <% if $CurrentMenu == 'calendarview' %>
		<div class="date-tabs">
			<a href="#" class="day btn">Day</a>
			<a href="#" class="week btn">Week</a>
			<a href="#" class="month current btn">Month</a>
		</div>
        <% end_if %>
	</div>

    <div id="calendar" class="fc fc-unthemed fc-ltr">
        <div class="fc-toolbar">
            <div class="fc-left"></div>
            <div class="fc-right">
                <a href="$PrevMonthLink" class="btn">
                    Previous</a>
                <a href="$NextMonthLink" class="btn">
                    Next</a></div>
            <div class="fc-center"><h2>December 2019</h2>
            </div><div class="fc-clear"></div></div>
    </div>
</div>
