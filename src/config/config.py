import json
import os
from os.path import exists


class Config:
    def __init__(self, dirPath, fileName):
        self.realPath = "{0}/{1}/{2}".format(os.getcwd(), dirPath, fileName)
        self.realParentPath = "{0}/{1}".format(os.getcwd(), dirPath)
        self.relativePath = "{0}/{1}".format(dirPath, fileName)
        self.config = {}

        if not exists(self.realParentPath):
            os.mkdir(self.realParentPath)

        if exists(self.realPath):
            self.load()
        else:
            with open(self.realPath, 'w') as writer:
                writer.write("{}")

        checkFor = [".gitignore", ".podmanignore", ".dockerignore"]

        for file in checkFor:
            fPath = os.getcwd() + "/" + file

            if not exists(fPath):
                with open(fPath, 'w') as writer:
                    writer.write(self.relativePath)
            else:
                with open(fPath, 'r') as reader:
                    if self.relativePath not in reader.read():
                        with open(fPath, 'a') as writer:
                            writer.write(self.relativePath)

    def load(self):
        with open(self.realPath, 'r') as reader:
            self.config = json.load(reader)

    def save(self):
        with open(self.realPath, 'w') as writer:
            json.dump(self.config, writer)

    def has(self, k):
        return k in self.config

    def set(self, k, v):
        self.config[k] = v
        self.save()

    def get(self, k):
        if self.has(k) is False:
            raise KeyError(
                    "\"{0}\" does not exist in {1}.".format(k, self.realPath))

        return self.config[k]

    def unset(self, k):
        if self.has(k):
            del self.config[k]
            self.save()


def config_with_defaults():
    return Config(".pop", "config.json")


def pop_config_set(k, v):
    cfg = config_with_defaults()
    cfg.set(k, v)


def pop_config_has(k):
    cfg = config_with_defaults()
    return cfg.has(k)


def pop_config_get(k):
    cfg = config_with_defaults()
    return cfg.get(k)
