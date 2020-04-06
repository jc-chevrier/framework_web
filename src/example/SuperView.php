<?php

namespace example;

class SuperView extends \simplifying\views\Template
{
    public function content()
    {
        $this->value('link-gf', "<a href='https://github.com/jc-chevrier/Simplifying'>Link to github of Simplifying framework.</a>");
        $this->value('link-gc', "<a href='https://github.com/jc-chevrier'>Link to github of creator's Simplifying framework.</a>");
        $this->value('link-ec', "<div>Email of creator's Simplifying framework : <b>chevrjc@gmail.com.</b></div>");
        return "<html lang='french'>
                        <head>
                                [[headers]]
                                [[scripts]]
                                [[css]]
                                <style>
                                    .row, .rows, .rows, .columns {
                                        display: flex;
                                    }
                                    .row, .rows {
                                        display: flex;
                                        flex-direction: row;
                                    }
                                    .rows, .columns {
                                        flex-wrap: wrap;
                                    }
                                    .column, .columns {
                                        flex-direction: column;
                                    }
                                    .menus {
                                        padding-top: 20px;
                                        width: 100%;
                                    }
                                    .menus > * {
                                        margin-left: 20px;
                                        margin-bottom: 20px;
                                        background-color: cornflowerblue;
                                        color: white;
                                        border: 1px solid blue;
                                        text-decoration: none;
                                        text-align: center;
                                        padding: 10px;
                                        font-size: 15px;
                                        font-style: italic;
                                        font-weight: bold;
                                    }
                                </style>
                        </head>
                        <body>
                                <hr>
                                <div class='row menus'>
                                      <a href=%%routes:BLANK%%>blank</a>
                                      <a href=%%routes:HOME%%>home</a>
                                      <a href=%%routes:NOTES%%>notes</a>
                                      <a href=%%routes:TREE%%>arbre</a>
                                      <a href=%%routes:ROUTES%%>routes</a>
                                      <a href=%%routes:CONTACT%%>contact</a>
                                </div>
                                <hr>
                                <br>
                                [[body]]
                        </body>
               </html>";
    }
}