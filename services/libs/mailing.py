from simplegmail import Gmail
from commons import CONFIG, MAIL_DIR, GOOGLE_SECRET_PATH
from jinja2 import Environment, FileSystemLoader, select_autoescape
from html.parser import HTMLParser

engine = Environment(
    loader=FileSystemLoader(MAIL_DIR),
    autoescape=select_autoescape(),
)

client = Gmail(client_secret_file=str(GOOGLE_SECRET_PATH))

class HTMLFilter(HTMLParser):
    text = ""
    def handle_data(self, data):
        self.text += data

def send_mail(to: list[str], template: str, subject: str, **kwargs):
    rtemplate = engine.get_template(f'{template}.jinja')
    html = rtemplate.render(**kwargs)

    reader = HTMLFilter()
    reader.feed(html)

    env = dict(
        sender='uwu.notification@gmail.com',
        to='',
        bcc=to,
        subject=subject,
        msg_html=html,
        msg_plain=reader.text
    )
    client.send_message(**env)
    
if __name__=='__main__':
    send_mail(
        ['jakubdakowski@gmail.com'],
        'test',
        'test'
    )