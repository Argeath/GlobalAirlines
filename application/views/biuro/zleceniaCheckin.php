<div class="row">
    <div class="col-xs-12">
        <div class="well">
            <div class="page-header">
                <h1>Zlecenie <small>Wybór punktu odpraw</small></h1>
            </div>

            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Z</th>
                    <th>Do</th>
                    <th>Zapłata</th>
                    <th>Pasażerów</th>
                    <th class="hidden-xs">Deadline</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?=Helper_Map::getCityName($zlecenie->order->from)?></td>
                    <td><?=Helper_Map::getCityName($zlecenie->order->to)?></td>
                    <td><?=formatCash($zlecenie->order->cash) . " " . WAL?></td>
                    <td><?=$zlecenie->order->count?></td>
                    <td class='hidden-xs'><?=Helper_TimeFormat::timestampToText($zlecenie->order->deadline)?></td>
                </tr>
                </tbody>
            </table>

            <table style="width: 100%;" class="table table-striped">
                <thead>
                <tr>
                    <th>Gracz</th>
                    <th><span id="tutorial_time">Skrócenie czasu odpraw</span></th>
                    <th><span id="tutorial_cost">Koszt za minute odprawy</span></th>
                    <th><span id="tutorial_totalTime">Czas odprawy</span></th>
                    <th><span id="tutorial_term">Najbliższy termin</span></th>
                    <th>Dodaj do kolejki</th>
                </tr>
                </thead>
                <tbody>
                    <? foreach($checks as $c)
                        {
                            $ch = $c['checkin'];
                            if( ! $ch->loaded())
                                continue;

                            $placeT = "";
                            if($c['place']) {
                                if($c['place'] < time() + 5)
                                    $placeT = Helper_Prints::colorBgText('TERAZ', 'green');
                                else
                                    $placeT = Helper_TimeFormat::timestampToText($c['place']);
                            } else
                                $placeT = Helper_Prints::colorBgText('Brak miejsc', 'red');

                            echo "<tr>";
                            echo "<td>".$ch->user->drawButton()."</td>";
                            echo "<td>".$c['bonus']."%</td>";
                            echo "<td>".(($ch->user_id == GlobalVars::$profil['id']) ? 0 : formatCash($ch->cost))." ".WAL."</td>";
                            echo "<td>".Helper_TimeFormat::secondsToText(round($c['odprawa']))."</td>";
                            echo "<td>".$placeT."</td>";
                            echo '<td>
                                '.Form::open('zlecenie/confirm').'
                                <input type="hidden" name="zlecenie" value="'.$zlecenieId.'" />
                                <input type="hidden" name="plane" value="'.$planeId.'" />
                                <input type="hidden" name="bazaPaliwo" value="'.$bazaPaliwo.'" />
                                <input type="hidden" name="checkin" value="'.$ch->id.'" />
                                <input type="hidden" name="planowany_start" value="'.$planowany_start.'" />
                                '.(($c['place']) ? "<button class='btn btn-primary btn-block'>Dodaj do kolejki</button>" : "").'
                                </form>
                                </td>';
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(function() {
        var n1 = new tutorialElement($('#tutorial_time'), "Hard_Bottom_Left", "Site_Bottom", "Im lepszy punkt odpraw, tym krótszy czas odprawy", "inner", false);
        var n2 = new tutorialElement($('#tutorial_cost'), "Site_Top", "Site_Top", "Opłata ustanowiona przez właścicela punktu odpraw, liczona za każdą minutę odprawy", "inner", false);
        var n3 = new tutorialElement($('#tutorial_totalTime'), "Site_Bottom", "Site_Bottom", "Obliczony czas, jaki zajmie odprawa w tym punkcie odpraw", "inner", false);
        var n4 = new tutorialElement($('#tutorial_term'), "Site_Top", "Site_Top", "Najbliższy termin odprawy w kolejce do tego punktu odpraw.", "inner", false);
        window.tutorialElements.push(n1, n2, n3, n4);
    });
</script>