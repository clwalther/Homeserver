class ENV:
    def __init__(self):
        self.file = self.read()
        
        self.PASSWORD = self.getValue("PASSWORD")
        self.EMAIL    = self.getValue("EMAIL")

    def read(self):
        with open('/home/pi/Development/Homeserver/env', 'r') as f:
            DATA = f.read()
        return DATA

    def getValue(self, KEY):
        VALUE = self.file.split(f"{KEY}:")
        VALUE = VALUE[1].split("\n")
        VALUE = VALUE[0].strip()

        return VALUE