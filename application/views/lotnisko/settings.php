<div class="row">
    <div class="col-xs-12">
        <div class="well">
            <div class="page-header">
                <h1>Lotnisko <small><?=$lotnisko->getName();?> (<?=$lotnisko->city->code?>)</small></h1>
            </div>
            <ul class="pagination pagination-xm">
                <li <?=($action == 'index') ? "class='active'" : "";?>><?=HTML::anchor('airport/index/' . $city_id, 'Lotnisko');?></li>
                <li <?=($action == 'offices') ? "class='active'" : "";?>><?=HTML::anchor('airport/offices/' . $city_id, 'Biura');?></li>
                <li <?=($action == 'stats') ? "class='active'" : "";?>><?=HTML::anchor('airport/stats/' . $city_id, 'Statystyki');?></li>
                <li <?=($action == 'office') ? "class='active'" : "";?>><?=HTML::anchor('airport/office/' . $office_id, 'Twoje biuro');?></li>
                <li <?=($action == 'settings') ? "class='active'" : "";?>><?=HTML::anchor('airport/settings/' . $checkin_id, 'Ustawienia punktu odpraw');?></li>
            </ul>
            <div class="clearfix"></div>

            <div class="well col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
                <div class="page-header">
                    <h3>Punkt Odpraw</h3>
                </div>
        <?=Form::open();?>
                    Cena <?=WAL?> za minute odprawy: <input type="text" name="cost" value="<?=$checkin->cost;?>" class="form-control"/>
                    Maksymalny czas rezerwacji ( w godzinach ): <input type="text" name="reservations" value="<?=$checkin->reservations / 3600;?>" class="form-control"/>
                    Maksymalny czas odprawy ( w minutach ): <input type="text" name="maxCheckin" value="<?=$checkin->maxCheckin / 60;?>" class="form-control"/>
                    Minimalny czas odprawy ( w minutach ): <input type="text" name="minCheckin" value="<?=$checkin->minCheckin / 60;?>" class="form-control"/>
                    <button class="btn btn-primary btn-block">Zapisz</button>
                </form>
            </div>

            <div class="clearfix"></div>

        </div>
    </div>
</div>