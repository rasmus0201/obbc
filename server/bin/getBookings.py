#!/usr/bin/python3
import sys
import requests
import json
import string
from bs4 import BeautifulSoup

def getBookings(username = '', password = ''):
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

    viewFrontpage = s.get('http://fitness.flexybox.com/obbc')

    html = BeautifulSoup(viewFrontpage.text, "html.parser")

    tableRows = html.select('#main table.ReservationTable tr')

    bookings = {};

    i = 0
    j = 0
    for tr in tableRows:
        if tr.find('th'): #Is "day" table row

            if len(tr.text.split(',')) > 1:
                date = tr.text.split(',')[1].strip()
            else:
                date = tr.text.strip()

            bookings[i] = {
                'date': date.replace('.', '/'),
                'bookings': {}
            }

            i += 1
            j = 0
        else:
            tds = tr.find_all('td')
            booking = {};

            k = 0
            for td in tds:
                if k == 0:
                    text = td.text.strip()

                    if 'du har ingen' in text.lower():
                        #User has no bookings
                        return {}

                    booking['time'] = td.text.strip()
                elif k == 1:
                    booking['name'] = td.text.strip()
                elif k == 2:
                    booking['room'] = td.text.strip()
                elif k == 3:
                    booking['trainer'] = td.text.strip()
                elif k == 4:
                    isText = td.text.strip()

                    if isText:
                        booking['waitList'] = isText
                    else:
                        booking['waitList'] = 0


                elif k == 5:
                    link = td.find('a', {'class': 'LeaveLink'})

                    if link:
                        s = link.get('onclick')
                        id = ''.join(x for x in s if x.isdigit())
                        booking['id'] = id
                    else:
                        booking['id'] = None

                k += 1

            bookings[i-1]['bookings'][j] = booking

            j += 1

    return bookings


def main():
    username = sys.argv[1]
    password = sys.argv[2]
    result = getBookings(username, password)
    print(json.dumps(result))


if __name__ == '__main__':
    main()
