<?php
/**
 * Plugin Countdown: Displays countdown from a specific date
 *                   Syntax: <COUNTDOWN:date|description>
 * date has to be formatted as GNU date (see strtotime)
 * e.g.              <COUNTDOWN:yyyy-mm-dd|description>
 *                   <COUNTDOWN:mm/dd/yyy|description>
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  Ekkart Kleinod <ekkart [at] ekkart.de> (V 2.x)
 * @author  Ron Peters <rbpeters [at] peterro.com> (V 1.0)
 * @author  Luis Machuca <luis [dot] machuca [at] gulix [dot] cl> (V 2.5+)
 * @version 3.0
 *
 */
// must be run within Dokuwiki
if (!defined('DOKU_INC')) {
    die();
}
if(!defined('DW_LF')) define('DW_LF',"\n");
require_once(DOKU_PLUGIN.'syntax.php');

/**
 * Plugin-Class for Countdown-Plugin.
 */
class syntax_plugin_countdown extends DokuWiki_Syntax_Plugin {

    function syntax_plugin_countdown () {
        global $ACT;
        if ($ACT==='show' && $this->getConf('time_message') > 0)
        msg ('Server time for Countdown plugin is: '. hsc(date('Y-m-d H:i')), 2);
    }

    function getType(){
        return 'substition';
    }

    public function getPType()
    {
        return 'normal';
    }

    function getSort(){
        return 721;
    }

    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\<COUNTDOWN\:.+?\>',$mode,'plugin_countdown');
    }

    // fix by Steffan Huehner
    function handle($match, $state, $pos, Doku_Handler $handler) {
        $stripped = substr($match,11,-1);
        // separate date from description
        $stripped = preg_split('/(?<!\\\\)\|/', $stripped, 3);
        // $stripped has the form <date>|<description>|<format>
        // convert all the escaped "\|" into "|"
        for ($i=0; $i < count($stripped); $i++) {
          $stripped[$i] = str_replace('\|', '|', $stripped[$i]);
          }
        return $stripped;
    }

    /**
     * Create output.
     */
	// fix by Steffan Huehner
    function render($mode, Doku_Renderer $renderer, $data) {
        list($dest, $descr, $fmt) = $data;
        $Hoy= new DateTimeImmutable();
        $Next= new DateTimeImmutable($dest);
        $dt_diff= date_diff($Hoy,$Next);
        $dt_days = $dt_diff->days * ($dt_diff->invert ? -1 : 1);
        if (!$descr) $descr = $this->getLang('nodesc');
/*
        $thatDate = strtotime ($dest);
        if ($thatDate <= 0) {
            $renderer->doc .= $this->getLang('wrongformat'). ': '. $dest. '.';
            return true;
        }
*/        
        // compose msg
        
        if ($dt_days == 0 && $this->getConf('use_today') ) {
            $Text1= 'Today '. $dt_diff->format("%H:%I");
            $WithHorus= false;
        } else {
            $ldays= $this->getLang('days');
            $Text1= $dt_diff->format();
            if ($this->getConf('with_hours')) {
                $Text1.=  $dt_diff->format("%a ${ldays} %H:%I");
            } else {
                $Text1.=  $dt_diff->format("%a ${ldays}");
            }
            
        }
        $TextArray[]= $Text1;
        $TextArray[]= ($dt_diff->invert ? $this->getLang('since') : $this->getLang('until'));
        $pdesc= p_render('xhtml', p_get_instructions($descr), $info);
        $pdesc= str_replace('<p>', '', $pdesc);
        $pdesc= str_replace('</p>', '', $pdesc);
        $TextArray[] = $pdesc;
        //$renderer->doc.= print_r($dt_diff, true);
        //$difdata= $this->compute($thatDate);
        $msg= implode(' ', $TextArray);
        if ('xhtml' === $mode) {
            $renderer->doc .= '<!-- countdown begin '. $data[0]. '-->';
            $renderer->doc .= '<span class="countdown">';
            $renderer->doc .= $msg; // sprintf("%d", $dt_days) . ' ';
                
            //if ($this->getConf('business_days') > 0) $renderer->doc .= sprintf("(%u)", $difdata['bdays']). ' ';
            //$renderer->doc .= ($difdata['days'] == 1) ? $this->getLang('oneday') : $this->getLang('days');
            //$renderer->doc .= ' ';
            // if with_hours, ... hh:mm left ...
            //if ($this->getConf('with_hours') ) $renderer->doc .= sprintf('%02d',$difdata['hours']). ':'. sprintf('%02d',$difdata['minutes']) . ' ';
            // "since" or "until"
            //$renderer->doc .= ($difdata['distance'] < 0) ? $this->getLang('since') : $this->getLang('until');
            //$renderer->doc .= ;


            //$renderer->doc .= $pdesc;
            // output date?
            if ($this->getConf('include_date') ) {
                $renderer->doc .= " (" . strftime($this->getLang('outputformat'), $Next->getTimestamp()) . ")";
            }
            // end tag
            $renderer->doc .= '</span><!-- countdown end -->'. DW_LF;
            return true;
        }
        else if ('text' === $mode) {
            $renderer->doc .= sprintf("%u %s %02u:%02u %s"
            , abs($difdata['days']), ($difdata['days'] == 1 ? $this->getLang('oneday') : $this->getLang('days') )
            , $difdata['hours'], $difdata['minutes']
            , ($difdata['distance'] < 0 ? $this->getLang('since') : $this->getLang('until') )
            );
            $renderer->doc .= ' '. p_render('text', p_get_instructions($description), $info);
            if ($this->getConf('include_date') ) {
                $renderer->doc .= " (" . strftime($this->getLang('outputformat'), $thatDate) . ")";
            }
            return true;
        }
        else
        {
            // no adequate renderer found
        }
        return false;
    }

    /**
     @brief Returns an array with the time difference to the specified timestamp.
    **/
    private function compute ($targetdate) {
        // compute date difference in days: 86400 = 24*60*60 = seconds of one day
        $t= time();
        $diffseconds = $targetdate - $t;
        $difference = $diffseconds / 86400; 

        return array(
            'distance' => $difference,
            'days'     => abs(($difference)),
            'bdays'    => $this->bdaysbetween( $t, $targetdate, array() ),
            'hours'    => abs($diffseconds / 3600) % 24,
            'minutes'  => abs($diffseconds / 60) % 60,
            'seconds'  => abs($diffseconds ) % 60
            );
    }

    /** @fn bdaysbetween
        @ brief Business days between date1 and date2
        Lifted from php.net
    **/
    private function bdaysbetween($startDate,$endDate,$holidays) {
    //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
    //We add one to inlude both dates in the interval.
    $days = ($endDate - $startDate) / 86400 + 1;

    $no_full_weeks = floor($days / 7);
    $no_remaining_days = fmod($days, 7);

    //It will return 1 if it's Monday,.. ,7 for Sunday
    $the_first_day_of_week = date("N",$startDate);
    $the_last_day_of_week = date("N",$endDate);

    //-->The two can be equal in leap years when february has 29 days, 
    //the equal sign is added here
    //In the first case the whole interval is within a week, 
    //in the second case the interval falls in two weeks.
    if ($the_first_day_of_week <= $the_last_day_of_week){
        if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) $no_remaining_days--;
        if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) $no_remaining_days--;
    }
    else{
        if ($the_first_day_of_week <= 6) {
        //In the case when the interval falls in two weeks, there will be a weekend for sure
            $no_remaining_days = $no_remaining_days - 2;
        }
    }

    //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
//---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
   $workingDays = $no_full_weeks * 5;
    if ($no_remaining_days > 0 )
    {
      $workingDays += $no_remaining_days;
    }

    //We subtract the holidays
    foreach($holidays as $holiday){
        $time_stamp=strtotime($holiday);
        //If the holiday doesn't fall in weekend
        if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7)
            $workingDays--;
    }

    return $workingDays;
    }


}
