#!/usr/bin/python3
import sys
import requests
import json
from bs4 import BeautifulSoup

def getWeek(username = '', password = '', weekStart = ''):
    creds = {
        'UserName': username,
        'Password': password,

        'RememberMe': 'true',
        'login': 'Login'
    }

    headers = {
        'Host': 'fitness.flexybox.com',
        'Origin': 'http://fitness.flexybox.com',
        'Upgrade-Insecure-Requests': '1',
        'DNT': '1',
        'Content-Type': 'application/x-www-form-urlencoded',
        'User-Agent': 'RasmusBundsgaard/OBBC UA 1.0',
        'Referer': 'http://fitness.flexybox.com/obbc/Account/LogOn'
    }
    s = requests.Session()

    s.headers = headers;

    loggedIn = s.post(
        'http://fitness.flexybox.com/obbc/Account/LogOn',
        data = creds
    )

    html = BeautifulSoup(loggedIn.text, "html.parser")

    if html.find('div', {'class': 'validation-summary-errors'}):
        return {'msg': 'Forkert brugernavn/adgangskode', 'status': 401}

    viewWeek = s.get('http://fitness.flexybox.com/obbc/TeamActivity/WeekView?dayInWeek='+weekStart)

    html = BeautifulSoup(viewWeek.text, "html.parser")

    days = html.select('table.allActiviesOuter > tr > td')

    week = {};

    i = 0
    for day in days:
        week[i] = {}

        dayHtml = BeautifulSoup(str(day), "html.parser")
        teams = dayHtml.select('table.weekRow tr td')

        j = 0
        for team in teams:
            tableData = BeautifulSoup(str(team), "html.parser")
            teamHtml = BeautifulSoup(str(tableData.select('div.TeamInfo')), "html.parser")

            futureActivity = False
            futureActivityExists = tableData.find('td', {'class': 'future-activity'})

            if futureActivityExists:
                futureActivity = teamHtml.find_all('div')[-1].get_text().strip()

                teamId = tableData.find_all('input')[0]

                if teamId:
                    teamId = teamId.get('value')

            else:
                teamId = teamHtml.find('img', {'class': 'TeamDesc'})

                if teamId:
                    teamId = teamId.get('data-id')

            if not teamId:
                continue

            teamTime = teamHtml.find('div', {'class': 'teamTime'})
            if teamTime:
                teamTime = teamTime.get('data-minutes')

            teamName = teamHtml.find('div', {'class': 'teamName'})

            if teamName:
                teamName = teamName.text

            teamRoom = teamHtml.find('div', {'class': 'room'})

            if teamRoom:
                teamRoom = teamRoom.text

            trainer = teamHtml.find('div', {'class': 'trainer'})
            trainerFullname = ''
            trainerName = ''

            if trainer:
                trainerFullname = trainer.get('data-full');
                trainerName = trainer.text;

            duration = teamHtml.find('div', {'class': 'duration'})

            if duration:
                duration = duration.text.replace(' min', '')

            doNotAllowBooking = teamHtml.find('div', {'class': 'DoNotAllowBooking'})

            if doNotAllowBooking or futureActivityExists:
                doNotAllowBooking = True
            else:
                doNotAllowBooking = False

            isCancelled = False
            trainingCancelledHtml = teamHtml.select('div.TeamInfo > div')

            for div in trainingCancelledHtml:
                divHtml = BeautifulSoup(str(div), "html.parser")
                divText = "".join(divHtml.text.split())
                if divText == 'Aflyst':
                    isCancelled = True

            isJoined = False
            isBooked = False
            numJoined = '0/0'
            innerTableHtmlTds = teamHtml.select('div.TeamInfo > table > tr > td')

            if len(innerTableHtmlTds) >= 2:
                numJoinedHtml = BeautifulSoup(str(innerTableHtmlTds[1]), "html.parser")
                waitList = numJoinedHtml.find('div', {'class': 'ui-icon-clock'})

                if waitList:
                    #numJoined = waitList.get('title')
                    isBooked = True

                numJoined = "".join(numJoinedHtml.text.split())

                isJoinedHtml = BeautifulSoup(str(innerTableHtmlTds[2]), "html.parser")
                tdText = "".join(isJoinedHtml.text.split())
                if tdText == 'Tilmeldt':
                    isJoined = True

            week[i][j] = {
                'id': teamId,
                'time': teamTime,
                'name': teamName,
                'room': teamRoom,
                'duration': duration,

                'trainerFullname': trainerFullname,
                'trainerName': trainerName,
                'numJoined': numJoined,

                'isBooked': isBooked,
                'isJoined': isJoined,
                'isCancelled': isCancelled,
                'isFutureActivity': futureActivity,
                'disallowBooking': doNotAllowBooking
            }

            j += 1

        i += 1

    return week


def main():
    username = sys.argv[1]
    password = sys.argv[2]
    weekStart = sys.argv[3]
    viewWeek = getWeek(username, password, weekStart)
    print(json.dumps(viewWeek))


if __name__ == '__main__':
    main()
