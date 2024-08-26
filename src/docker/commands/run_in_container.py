import os


class RunInContainer:
    def __init__(
        self,
        image,
        cmd,
        containerName,
        volumes=[],
        env={},
        pseudoTTY=False,
        interactive=False,
        removeAfter=True,
        options={}
    ):
        self.image = image
        self.env = env
        self.args = cmd
        self.containerName = containerName
        self.volumes = volumes
        self.pseudoTTY = pseudoTTY
        self.interactive = interactive
        self.removeAfter = removeAfter
        self.options = options

    def cmd(self):
        args = ['run', '--name', self.containerName]

        if self.pseudoTTY:
            args.append("-t")

        if self.interactive:
            args.append("-i")

        if self.removeAfter:
            args.append("--rm")

        for k, v in self.options.items():
            args.append(k)
            args.append(v)

        for k, v in self.env.items():
            args.append("--env")
            args.append("{0}={1}".format(k, v))

        for source, target in self.volumes.items():
            args.append("-v")
            args.append("{0}:{1}".format(source, target))

        args.append(self.image.full_path())

        for v in self.args:
            args.append(v)

        return args
