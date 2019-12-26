from pyrogram import Client, Filters
import time
from time import sleep
import sys
sys.path.append("..")
import main

logs = main.logs
@Client.on_message(Filters.audio & ~Filters.chat(logs))
def send_music(c, m):
    print(m)
    try:
        c.forward_messages(chat_id = logs, from_chat_id = m.chat.id, message_ids = m.message_id)
    except Exception as e:
        m.reply(str(e))


@Client.on_message(Filters.command('join', ''))
def join(c, m):
    try:
        c.join_chat('{}'.format(m.command[1]))
        m.reply('#Ok')

    except Exception as e:
        m.reply('__{}__'.format(e), parse_mode='MARKDOWN')
@Client.on_message(Filters.command('startfrom', ''))
def send_xyz(c, m):
    x = int(m.command[1])
    u = 1;
    m.reply('#Starting from {}'.format(x))
    while True:
        y = '/downloada_'+str(x)
        c.send_message('@moozikestan_bot',y)
        x += 1
        u += 1

        time.sleep(15)
        if u == 240:
            c.send_message(logs,'ربات به دلیل محدود نشدن از سمت تلگرام و دلیت اکانت شدن به مدت 15 دقیقه  به حالت sleep می رود');
            time.sleep(900);
            u=1
@Client.on_message(Filters.command('ping', '/'))
def send_pong(c, m):

    m.reply('#Pong')
