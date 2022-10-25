from envelopes import GMailSMTP, Envelope
from commons import CONFIG
    
client = GMailSMTP(CONFIG['gmail_address'], CONFIG['gmail_password'])

HTML_TEMPLATE = """

"""
TEXT_TEMPLATE = """

"""

def send_mail(to: list[str], subject: str, **kwargs):
    env = Envelope(
        bcc_addr=to,
        from_addr=CONFIG['gmail_address'],
        subject=subject,
        html_body=HTML_TEMPLATE.format(**kwargs),
        text_body=TEXT_TEMPLATE.format(**kwargs)
    )
    client.send(env)