from pyrogram import Client, Filters

api_id = 222720
api_hash = "40f8bd8b866ea2272fe57bcec76a9818"


logs = -1001275244057

client = Client('music', api_id, api_hash, plugins_dir = 'plugins').run()
