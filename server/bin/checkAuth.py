#!/usr/bin/python3

import sys
import requests
import json
from bs4 import BeautifulSoup

def getAuth(username = '', password = ''):
    creds = {
        'UserName': username,
        'Password': password,

        'RememberMe': 'true',
        'login': 'Login'
    }

    #Old: 'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36',
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

    return {'msg': 'Logget ind', 'status': 200}


def main():
    username = sys.argv[1]
    password = sys.argv[2]
    isAuth = getAuth(username, password)
    print(json.dumps(isAuth))


if __name__ == '__main__':
    main()
