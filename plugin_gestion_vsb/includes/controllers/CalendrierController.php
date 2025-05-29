<?php
namespace VSB\controllers;

use VSB\repositories\CalendrierRepository;

class CalendrierController {

    public function registerShortcodes() {
        add_shortcode('vsb_calendrier', [$this, 'afficherCalendrier']);
    }

    public function afficherCalendrier() {
        $repo = new CalendrierRepository();
        $evenements = $repo->getEvenementsMatchs();
        $json = json_encode($evenements);
    
        ob_start();
        ?>
        <div id="vsb-calendar"></div>
    
        <!-- MODAL -->
        <div id="vsb-modal" class="vsb-modal">
            <div class="vsb-modal-content">
                <span class="vsb-close">&times;</span>
                <h2 id="vsb-modal-title"></h2>
                <p><strong>Date :</strong> <span id="vsb-modal-date"></span></p>
                <p><strong>Heure :</strong> <span id="vsb-modal-heure"></span></p>
                <p><strong>Lieu :</strong> <span id="vsb-modal-lieu"></span></p>
                <p id="vsb-modal-score" style="display:none;"></p>
            </div>
        </div>
    
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/fr.global.min.js"></script>

    
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('vsb-calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                events: <?= $json ?>,
                eventClick: function(info) {
                const e = info.event;
                const props = e.extendedProps;

                document.getElementById('vsb-modal-title').textContent = e.title;
                document.getElementById('vsb-modal-date').textContent = e.start.toLocaleDateString();
                document.getElementById('vsb-modal-heure').textContent = e.start.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                document.getElementById('vsb-modal-lieu').textContent = props.location || "Non précisé";

                // Gérer l'affichage du score
                const scoreP = document.getElementById('vsb-modal-score');
                if (props.is_past && props.score_equipe !== null && props.score_adverse !== null) {
                    scoreP.innerHTML = "<strong>Score :</strong> " + props.score_equipe + " - " + props.score_adverse;
                    scoreP.style.display = 'block';
                } else {
                    scoreP.style.display = 'none';
                }

                document.getElementById('vsb-modal').style.display = 'block';
            },
                eventDidMount: function(info) {
                    const timeEl = info.el.querySelector('.fc-event-time');
                    const titleEl = info.el.querySelector('.fc-event-title');

                    if (timeEl && titleEl) {
                        // Forcer l’ordre : time avant title
                        info.el.innerHTML = '';
                        info.el.appendChild(timeEl);
                        info.el.appendChild(titleEl);
                    }

                    info.el.style.whiteSpace = 'normal';
                    info.el.style.overflowWrap = 'anywhere';
                    info.el.style.wordBreak = 'break-word';
                }
            });
            calendar.render();
    
            // Modal logic
            const modal = document.getElementById('vsb-modal');
            document.querySelector('.vsb-close').onclick = () => modal.style.display = "none";
            window.onclick = (e) => { if (e.target == modal) modal.style.display = "none"; };
        });
        </script>
    
        <style>
            #vsb-calendar {
                max-width: 900px;
                margin: 0 auto;
            }
            .vsb-modal {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0; top: 0;
                width: 100%; height: 100%;
                background-color: rgba(0,0,0,0.4);
            }
            .vsb-modal-content {
                background-color: white;
                margin: 10% auto;
                padding: 20px;
                border-radius: 8px;
                width: 80%;
                max-width: 400px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            }
            .vsb-close {
                float: right;
                font-size: 22px;
                font-weight: bold;
                cursor: pointer;
            }
            .fc-event-title,
            .fc-event-time {
                white-space: normal !important;
                overflow-wrap: anywhere !important;
                word-break: break-word;
                font-size: 13px;
                line-height: 1.2;
                text-align: center;
            }

            .fc-event {
                padding: 2px 4px !important;
            }
            .fc-event-time {
                display: block !important;
                font-weight: bold;
                color: #7cda24;
                margin-bottom: 2px;
            }

            .fc-event-title {
                display: block;
                white-space: normal !important;
                overflow-wrap: anywhere;
                font-size: 13px;
                line-height: 1.2;
            }

            /* Agrandir les cellules du calendrier */
            .fc .fc-daygrid-day-frame {
                padding: 5px;
                min-height: 80px;
            }

            .fc .fc-daygrid-event {
                white-space: normal !important;
                word-break: normal !important;
                font-size: 13px;
                line-height: 1.2;
                padding: 2px;
                width: 100%; /* occupe toute la largeur de la cellule */
                display: block;
            }
            #vsb-calendar .fc-daygrid-day-frame {
                padding: 5px;
                min-height: 80px;
            }

            #vsb-calendar .fc-daygrid-event {
                white-space: normal !important;
                word-break: break-word;
                font-size: 13px;
                line-height: 1.2;
                padding: 2px 4px;
                display: block;
            }

            /* Pour forcer un scroll horizontal au besoin */
            #vsb-calendar .fc-view-harness {
                overflow-x: auto;
            }

        </style>
        <?php
        return ob_get_clean();
    }
    
}
