import sys
import smtplib
from email.message import EmailMessage
from Env import ENV


def main():
    global ENV
    env = ENV()

    FILE     = sys.argv[0]
    TYPE     = sys.argv[1]
    RECEIVER = sys.argv[2]

    CODE     = sys.argv[3]

    msg = EmailMessage()
    msg['Subject'] = 'Authenticate'
    msg['From']    = 'Homeserver'
    msg['To']      = RECEIVER

    if TYPE == "SIGNUP":
        CODE = int(CODE)
        if CODE > 99999 and CODE < 1000000:
            msg.set_content(f"\nCODE: {CODE}\n\nIf you have not tried to create an account for homeserver you can ignore this E-Mail.")

    elif TYPE == "LOGIN":
        msg.set_content(f"\n2FAUTH: {CODE}")

    with smtplib.SMTP_SSL('smtp.gmail.com',465) as smtp:
        smtp.login(env.EMAIL, env.PASSWORD)
        smtp.send_message(msg)

if __name__ == '__main__':
    main()