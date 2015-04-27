<div class="well">
    <div class="page-header">
        <h1>Lotnisko <small><?=$lotnisko->getName();?> (<?=$lotnisko->code?>)</small></h1>
    </div>
    <ul class="pagination pagination-xm">
        <li <?=($action == 'index') ? "class='active'" : "";?>><?=HTML::anchor('airport/index/' . $city_id, 'Lotnisko');?></li>
        <li <?=($action == 'offices') ? "class='active'" : "";?>><?=HTML::anchor('airport/offices/' . $city_id, 'Biura');?></li>
        <li <?=($action == 'stats') ? "class='active'" : "";?>><?=HTML::anchor('airport/stats/' . $city_id, 'Statystyki');?></li>
        <li <?=($action == 'office') ? "class='active'" : "";?>><?=HTML::anchor('airport/office/' . $office_id, 'Twoje biuro');?></li>
        <li <?=($action == 'activity') ? "class='active'" : "";?>><?=HTML::anchor('airport/activity/' . $checkin_id, 'Aktywność');?></li>
    </ul>
    <div class="clearfix"></div>

    <div class="hidden-xs">
        <div class="thumbnail col-sm-12">
            <div class="suwakDiv">
                <div id="terminarzDate" class="input-group date col-xs-12 col-sm-6 col-md-4" style="float: none; margin: 20px auto;">
                    <input id="terminarzDateInput" data-format="dd.MM.yyyy" type="text" name="terminarzDate" class="form-control"></input>
					<span class="input-group-addon add-on">
						<i id="terminarzDateButton" data-time-icon="icon-time" data-date-icon="icon-calendar" class="glyphicon glyphicon-calendar"></i>
					</span>
                </div>
            </div>
            <? for($i=1; $i <= 7; $i++) { ?>
                <div class="suwakDiv">
                    <div class="suwakDzien col-md-2"><?=$dni[$i]['name'];?><br /><?=$dni[$i]['date'];?></div>
                    <div class="suwakBlock col-md-10">
<?=$dni[$i]['zlecenia'];?>
<div class="suwak"></div>
<?=$textT;?>
                        <? if($dni[$i]['today'])
                            echo "<div class='suwakGodzina' style='left: ".$suwakGodzinaPoz."px;'></div>"; ?>
                    </div>
                </div>
            <? } ?>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="visible-xs">
        Terminarz nie jest dostępny w tej rozdzielczości ekranu.
    </div>

    <div class="clearfix"></div>
</div>

<script>
    $(function()
    {
        $("#terminarzDateInput").datetimepicker({
            value:"<?=$dateText;?>",
            lang:"pl",
            timepicker:false,
            format:"d.m.Y",
            onChangeDateTime:function(dp,$input){
                var parts = $input.val().split(".");
                var date = new Date(parts[2], parseInt(parts[1], 10) - 1, parts[0], 0, 0);
                location.href = url_base() + "airport/activity/<?=$checkin_id;?>/" + date.getTime()/1000;
            }
        });

        $("#terminarzDateButton").click(function(){
            $("#terminarzDateInput").datetimepicker("show");
        });
    })
</script>